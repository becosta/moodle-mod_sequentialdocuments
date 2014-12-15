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
include_once __DIR__.'/../dao/feedback_dao.php';
include_once __DIR__.'/../entity/feedback.php';
include_once __DIR__.'/../../../locallib.php';

class feedback_manager extends manager {

    public function __construct(array $data = null) {
        parent::__construct($data);
        $this->dao = new feedback_dao();
    }

    public function create_feedback(stdClass $data, version_manager $versionmanager) {
        global $DB;
        $data->creationtime = time();
        $data->feedbackindice =
                $DB->count_records(
                    'sequentialdocuments_feedback',
                    array('versionid' => $data->versionid)
                ) + 1
        ;
        $feedback = $this->get_entity_instance_from_stdClass('feedback', $data);
        return $this->dao->insert($feedback);
    }

    public function update_feedback($feedbackid, stdClass $data) {
        global $DB;
        $feedback = $this->dao->get_entity($feedbackid);

        if (!($feedback instanceof feedback)) {
            throw new InvalidArgumentException();
        } else if ($feedback->get_instanceid() != $this->instanceid) {
            throw new unauthorized_access_exception(1000, 'mod_sequencialdocuments');
        }

        $vars = get_object_vars($data);
        foreach ($vars as $property => $value) {
            $setter = 'set_'.$property;
            if (is_callable(array($feedback, $setter))) {
                $feedback->$setter($value);
            }
        }
        $this->dao->update($feedback);
    }

    public function lock_feedbacks_by_version(version $version, $userid) {

        if (!sequentialdocuments_has_lock_feedback_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception(1006, 'mod_sequentialdocuments');
        }

        $feedbacks = $this->dao->get_all_entities_where(array('versionid' => $version->get_id()));
        if ($feedbacks !== false) {
            foreach ($feedbacks as $feedback) {
                $feedback->set_locked(true);
                $this->dao->update($feedback);
            }
        }
    }

    public function is_last_feedback(feedback $feedback) {
        global $DB;
        $count = $DB->count_records(
                    'sequentialdocuments_version',
                    array('versionid' => $feedback->get_versionid())
                )
        ;
        if ($feedback->get_feedbackindice() == $count) {
            return true;
        }
        return false;
    }

    public function get_feedback_by_id($id) {
        return $this->dao->get_entity($id);
    }

    public function get_feedbacks_by_versionid($id) {
        return $this->dao->get_all_entities_where(array('versionid' => $id));
    }

    public function delete_feedback($feedbackid, $userid) {
        $feedback = $this->dao->get_entity($feedbackid);

        if (!($feedback instanceof feedback)) {
            throw new InvalidArgumentException();
        } else if (!$feedback->get_instanceid() == $this->instanceid) {
            throw new unauthorized_access_exception(1000, 'mod_sequencialdocuments');
        }

        if (!sequentialdocuments_has_feedback_suppression_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception(1003, 'mod_sequentialdocuments');
        }

        $this->dao->delete($feedback);
    }

    public function delete_feedbacks_by_versionid($versionid, $userid) {

        if (!sequentialdocuments_has_version_suppression_rights($this->instanceid, $userid) ||
                !sequentialdocuments_has_feedback_suppression_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception(1003, 'mod_sequentialdocuments');
        }

        $feedbacks = $this->dao->get_all_entities_where(array('versionid' => $versionid));
        if ($feedbacks !== false) {
            foreach ($feedbacks as $feedback) {
                $this->dao->delete($feedback);
            }
        }
    }

    public function get_feedback_html_by_id(
                                            $id,
                                            $userid,
                                            document_manager $documentmanager,
                                            version_manager $versionmanager,
                                            $contextid) {

        if (!sequentialdocuments_has_feedback_read_rights($this->instanceid, $userid)) {
            return '';
        }

        return $this->get_feedback_html_by_feedback_instance(
							$this->dao->get_entity($id),
							$userid,
							$documentmanager,
							$versionmanager,
							$contextid
        );
    }

    public function get_feedback_html_by_feedback_instance(
                                                            feedback $feedback,
                                                            $userid,
                                                            document_manager $documentmanager,
                                                            version_manager $versionmanager,
                                                            $contextid) {
        if ($feedback === null) {
            throw InvalidArgumentException();
        }

        if ($feedback->get_instanceid() != $this->instanceid) {
            throw new unauthorized_access_exception(1000, 'mod_sequencialdocuments');
        }

        if (!sequentialdocuments_has_feedback_read_rights($this->instanceid, $userid)) {
            return '';
        }

        $setnewversionlink = '';
        $filelist = '';
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'mod_sequentialdocuments', 'feedback', $feedback->get_id());

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
                $filelist .= '<br /><a href="'.$url.'">'.$filename.'</a>';
            }
        }

        if ($filelist != '') {
            $filelist = '<strong>Attachments: </strong>'.$filelist;

            // TODO : implement the version creation from feedback function.
            $version = $versionmanager->get_version($feedback->get_versionid());
            if (!$version->is_locked() &&
                    $versionmanager->is_last_version($documentmanager, $version) &&
                    sequentialdocuments_has_version_creation_rights($this->instanceid, $userid)) {
                $filelist .= '<br /><br /><a href="#">Create new version from this feedback</a>';
            }
        }

        $links = '';
        if (!$feedback->is_locked()) {
            if (sequentialdocuments_has_feedback_edit_rights($this->instanceid, $userid)) {
                $links .=
                    '<a href="'.
                        get_update_feedback_url($feedback->get_id(), $this->instanceid).
                        '">Edit</a> ';
            }
            if (sequentialdocuments_has_feedback_suppression_rights($this->instanceid, $userid)) {
                    $links .=
                        '<a href="'.
                            get_delete_feedback_url($feedback->get_id(), $this->instanceid).
                            '">Delete</a>';
            }
        }

        $links = '<aside class="sqds-bottom-right">'.
                    $links.
                '</aside>';

        return  '<section class="sqds-feedback">'.$feedback->get_html().$filelist.$links.'</section>';
    }
}


