<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package    mod_sequentialdocuments
 * @author     Benjamin Costa
 * @copyright  (c) 2014 CNAM, Paris, France
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

include_once __DIR__.'/manager.php';
include_once __DIR__.'/feedback_manager.php';
include_once __DIR__.'/../dao/version_dao.php';
include_once __DIR__.'/../entity/version.php';
include_once __DIR__.'/../entity/feedback.php';
include_once __DIR__.'/../exception/unauthorized_access_exception.php';
include_once __DIR__.'/../../../locallib.php';

class version_manager extends manager {

    const ENTITY_NAME = 'version';

    public function __construct(array $data = null) {
        parent::__construct($data);
        $this->dao = new version_dao();
    }

    public function create_version(
                                    $data,
                                    $contextid,
                                    document_manager $documentmanager,
                                    feedback_manager $feedbackmanager) {
        global $USER;

        $timestamp = time();
        $data->creationtime = $timestamp;

        $version = $this->get_entity_instance_from_stdClass('version', $data);

        $previous = $this->get_previous_version($version);
        if (!$previous) {
            $document = $documentmanager->get_document($data->documentid);
            $previous = $this->dao->get_entity($document->get_currentversionid());
        }
        if ($previous) {
            $this->lock($previous, $USER->id, true);
            $feedbackmanager->lock_feedbacks_by_version($previous, $USER->id, true);
            $version->set_versionindice($previous->get_versionindice() + 1);
        }

        $versionid = $this->dao->insert($version);
        $documentmanager->set_new_document_version($data->documentid, $versionid);

        $this->save_entity_draft_area_file($versionid, $contextid, $data->attachments);

        return $versionid;
    }

    public function create_first_document_version(stdClass $data) {

        $version = new version();
        $version->set_instanceid($data->instanceid);
        $version->set_documentid($data->documentid);
        $version->set_versionindice(1);
        $version->set_creationtime($data->timestamp);
        return $this->dao->insert($version);
    }

    public function create_from_feedback(
                                            $documentid,
                                            $feedbackid,
                                            $contextid,
                                            document_manager $documentmanager,
                                            feedback_manager $feedbackmanager) {
        global $USER;


        $data = new stdClass();
        $data->instanceid = $this->instanceid;
        $data->documentid = $documentid;
        $data->creationtime = time();
        $data->duetime = -1;
        $data->duevalidated = 0;
        $data->locked = 0;
        $data->attachments =
                $feedbackmanager->get_entity_draft_area($feedbackid, $contextid)->attachments;

        return $this->create_version($data, $contextid, $documentmanager, $feedbackmanager);
    }

    public function update_version($versionid, stdClass $data, $contextid) {

        global $DB;
        $version = $this->dao->get_entity($versionid);

        if (!($version instanceof version)) {
            throw new InvalidArgumentException();
        } else if ($version->get_instanceid() != $this->instanceid) {
            throw new unauthorized_access_exception('uainstance', 'mod_sequentialdocuments');
        }

        $vars = get_object_vars($data);
        foreach ($vars as $property => $value) {
            $setter = 'set_'.$property;
            if (is_callable(array($version, $setter))) {
                $version->$setter($value);
            }
        }
        $this->dao->update($version);

        $this->save_entity_draft_area_file($versionid, $contextid, $data->attachments);
    }

    public function delete_version(
                                    $versionid,
                                    $userid,
                                    document_manager $documentmanager,
                                    feedback_manager $feedbackmanager) {

        $version = $this->dao->get_entity($versionid);

        if (!($version instanceof version)) {
            throw new InvalidArgumentException();
        } else if (!$version->get_instanceid() == $this->instanceid) {
            throw new unauthorized_access_exception('uainstance', 'mod_sequentialdocuments');
        }

        if (!sequentialdocuments_has_version_suppression_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception('ua1002', 'mod_sequentialdocuments');
        }

        if ($this->is_last_version($documentmanager, $version)) {
            $documentmanager->notify_version_deletion($this, $version);
        }
        $feedbackmanager->delete_feedbacks_by_versionid($versionid, $userid);
        $this->dao->delete($version);

        // TODO : call reminder_manager.
        $reminderdao = new reminder_dao();
        $reminders = $reminderdao->get_all_entities_where(array('versionid' => $versionid));
        if ($reminders !== false) {
            foreach ($reminders as $reminder) {
                $reminderdao->delete($reminder);
            }
        }
    }

    public function lock(version $version, $userid, $ignoreaccesscontrol = false) {

        if (!$ignoreaccesscontrol && !sequentialdocuments_has_lock_version_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception('ua1005', 'mod_sequentialdocuments');
        }

        $version->set_locked(true);
        $this->dao->update($version);
    }

    public function unlock(version $version, $userid) {

        if (!sequentialdocuments_has_lock_version_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception('ua1005', 'mod_sequentialdocuments');
        }

        $version->set_locked(false);
        $this->dao->update($version);
    }

    public function is_last_version(document_manager $documentmanager, version $version) {
        $document = $documentmanager->get_document($version->get_documentid());
        if ($document->get_currentversionid() == $version->get_id()) {
            return true;
        }
        return false;
    }

    public function get_previous_version($version) {
        $indice = $version->get_versionindice();
        if ($indice <= 1) {
            return false;
        }

        return $this->dao->get_entity_where(
                  array('documentid' => $version->get_documentid(), 'versionindice' => $indice - 1)
        );
    }

    public function get_version($id) {
        return $this->dao->get_entity($id);
    }

    public function get_versions_by_documentid($id) {
        return $this->dao->get_all_entities_where(array('documentid' => $id));
    }

    public function delete_versions_by_documentid($documentid, $userid, $feedbackmanager) {

        if (!sequentialdocuments_has_document_suppression_rights($this->instanceid, $userid) ||
            !sequentialdocuments_has_version_suppression_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception('ua1002', 'mod_sequentialdocuments');
        }

        $reminderdao = new reminder_dao();
        $versions = $this->dao->get_all_entities_where(array('documentid' => $documentid));
        if ($versions !== false) {
            foreach ($versions as $version) {
                // TODO : call reminder_manager.
                $reminders = $reminderdao->get_all_entities_where(array('versionid' => $version->get_id()));
                if ($reminders) {
                    foreach ($reminders as $reminder) {
                        $reminderdao->delete($reminder);
                    }
                }
                $feedbackmanager->delete_feedbacks_by_versionid($version->get_id(), $userid);
                $this->dao->delete($version);
            }
        }
    }

    public function get_current_document_version(document $document) {
        return $this->dao->get_entity($document->get_currentversionid());
    }

    public function get_version_html_by_id(
                                            $id,
                                            $userid,
                                            document_manager $documentmanager,
                                            feedback_manager $feedbackmanager,
                                            $contextid) {

        if (!sequentialdocuments_has_version_read_rights($this->instanceid, $userid)) {
            return '';
        }

        $version = $this->dao->get_entity($id);
        if ($version === false) {
            throw new inexistent_entity_exception('ueversion', 'mod_sequentialdocuments');
        }

        return $this->get_version_html_by_version_instance(
							$version,
							$userid,
							$documentmanager,
							$feedbackmanager,
							$contextid
        );
    }

    public function get_version_html_by_version_instance(
                                                            version $version,
                                                            $userid,
                                                            document_manager $documentmanager,
                                                            feedback_manager $feedbackmanager,
                                                            $contextid) {

        if ($version === null) {
            throw new InvalidArgumentException();
        }

        if ($version->get_instanceid() != $this->instanceid) {
            throw new unauthorized_access_exception('uainstance', 'mod_sequentialdocuments');
        }

        if (!sequentialdocuments_has_version_read_rights($this->instanceid, $userid)) {
            return '';
        }

        $html = '';
        $feedbacks = $feedbackmanager->get_feedbacks_by_versionid($version->get_id());

        if (count($feedbacks) > 1 || (count($feedbacks) == 1 && $feedbacks[0] != false)) {
            foreach ($feedbacks as $feedback) {
                $html .= $feedbackmanager->get_feedback_html_by_feedback_instance(
                                            $feedback, $userid, $documentmanager, $this, $contextid
                );
            }
        }

        $filelist = '<br />';
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'mod_sequentialdocuments', 'version', $version->get_id());

        if ($files !== false) {
            foreach ($files as $file) {
                $filename = $file->get_filename();

                if ($filename == '.') {
                    continue;
                }

                $url = moodle_url::make_pluginfile_url(
                                        $file->get_contextid(),
                                        $file->get_component(),
                                        $file->get_filearea(),
                                        $file->get_itemid(),
                                        $file->get_filepath(),
                                        $filename
                );
                $filelist .= '<a href="'.$url.'">'.$filename.'</a><br />';
            }
        }

        $links = '';
        $versionid = $version->get_id();
        $instanceid = $version->get_instanceid();
        if (!$version->is_locked() || $version->needs_user_submission()) {

            if ((!$version->is_locked() &&
                    sequentialdocuments_has_version_edit_rights($this->instanceid, $userid)) ||
                    $version->needs_user_submission()) {
                if (sequentialdocuments_current_user_is_instance_student($instanceid)) {
                    $links .=
                            '<a href="'.get_update_version_url($versionid, $instanceid).'">'.
                                get_string('versionaddfilelink', 'mod_sequentialdocuments').
                            '</a> ';
                } else {
                    $links .=
                            '<a href="'.get_update_version_url($versionid, $instanceid).'">'.
                                get_string('versioneditlink', 'mod_sequentialdocuments').
                            '</a> ';
                }
            }

            if ((!$version->is_locked() &&
                    sequentialdocuments_has_feedback_creation_rights($this->instanceid, $userid)) ||
                    $version->needs_user_submission()) {
                $links .=
                        '<a href="'.get_add_feedback_url($versionid, $instanceid).'">'.
                            get_string('versionpostfeedbacklink', 'mod_sequentialdocuments').
                        '</a> ';
            }

        }
        if (!$version->is_locked()) {

            if (sequentialdocuments_has_lock_version_rights($instanceid, $userid)) {
                $links =
                        '<a href="'.get_lock_version_url($versionid, $instanceid).'">'.
                            get_string('versionlocklink', 'mod_sequentialdocuments').
                        '</a> '.$links;
            }

            if (sequentialdocuments_has_version_suppression_rights($this->instanceid, $userid)) {
                $links .=
                    '<a href="'.get_delete_version_url($versionid, $instanceid).
                        '" onclick="return confirm(\''.
                        get_string('versiondeleteconfirmation', 'mod_sequentialdocuments').'\');">'.
                        get_string('versiondeletelink', 'mod_sequentialdocuments').
                    '</a>';
            }
        } else {

            if (sequentialdocuments_has_lock_version_rights($instanceid, $userid)) {
                $links =
                        '<a href="'.get_unlock_version_url($versionid, $instanceid).'">'.
                            get_string('versionunlocklink', 'mod_sequentialdocuments').
                        '</a> '.$links;
            }
        }

        $lastversionattr = '';
        if ($this->is_last_version($documentmanager, $version)) {
            $document = $documentmanager->get_document($version->get_documentid());
            if ($documentmanager->is_last_document($document)) {
                $lastversionattr = 'data-sqds-is-last';
            }
        }

        return  '<section class="sqds-version">'.
                    '<a onclick="toggle_visibility(\'sqds-version-content-'.$versionid.'\')">'.
                        get_string('versionlink', 'mod_sequentialdocuments').$version->get_versionindice().
                    '</a>'.
                    '<div id="sqds-version-content-'.$versionid.
                    '" class="sqds-version-content" '.$lastversionattr.'>'.
                        $version->get_html().'<br />'.$filelist.$html.
                        '<aside class="sqds-bottom-right">'.
                            $links.
                        '</aside>'.
                    '</div>'.
                '</section>';
    }
}


