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

    public function __construct(array $data = null) {
        parent::__construct($data);
        $this->dao = new version_dao();
    }

    public function create_version($data, document_manager $documentmanager, feedback_manager $feedbackmanager) {
        global $DB;

        $timestamp = time();
        $data->creationtime = $timestamp;

        $version = $this->get_entity_instance_from_stdClass('version', $data);

        $previous = $this->get_previous_version($version);
        if (!$previous) {
            $document = $documentmanager->get_document($data->documentid);
            $previous = $this->dao->get_entity($document->get_currentversionid());
        }
        if ($previous) {
            $this->lock($previous);
            $feedbackmanager->lock_feedbacks_by_version($previous);
            $version->set_versionindice($previous->get_versionindice() + 1);
        }

        $versionid = $this->dao->insert($version);
        $documentmanager->set_new_document_version($data->documentid, $versionid);

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

    public function update_version($versionid, stdClass $data) {

        global $DB;
        $version = $this->dao->get_entity($versionid);

        if (!($version instanceof version)) {
            throw new InvalidArgumentException();
        } else if ($version->get_instanceid() != $this->instanceid) {
            throw new unauthorized_access_exception(1000, 'mod_sequencialdocuments');
        }

        $vars = get_object_vars($data);
        foreach ($vars as $property => $value) {
            $setter = 'set_'.$property;
            if (is_callable(array($version, $setter))) {
                $version->$setter($value);
            }
        }
        $this->dao->update($version);
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
            throw new unauthorized_access_exception(1000, 'mod_sequencialdocuments');
        }

		if (!sequentialdocuments_has_version_suppression_rights($this->instanceid, $userid)) {
			throw new unauthorized_access_exception(1002, 'mod_sequentialdocuments');
		}

        if ($this->is_last_version($documentmanager, $version)) {
            $documentmanager->notify_version_deletion($this, $version);
        }
        $feedbackmanager->delete_feedbacks_by_versionid($versionid, $userid);
        $this->dao->delete($version);
    }

    public function lock(version $version, $userid) {

		if (!sequentialdocuments_has_lock_version_rights($this->instanceid, $userid)) {
			throw new unauthorized_access_exception(1005, 'mod_sequentialdocuments');
		}

        $version->set_locked(true);
        $this->dao->update($version);
    }

    public function unlock(version $version, $userid) {

		if (!sequentialdocuments_has_lock_version_rights($this->instanceid, $userid)) {
			throw new unauthorized_access_exception(1005, 'mod_sequentialdocuments');
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
			throw new unauthorized_access_exception(1002, 'mod_sequentialdocuments');
		}

        $versions = $this->dao->get_all_entities_where(array('documentid' => $documentid));
        if ($versions !== false) {
            foreach($versions as $version) {
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

        return $this->get_version_html_by_version_instance(
							$this->dao->get_entity($id),
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
            throw new unauthorized_access_exception(1000, 'mod_sequencialdocuments');
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

        $editlink = '';
        $postfeedbacklink = '';
        if (!$version->is_locked()) {
			if (sequentialdocuments_has_version_edit_rights($this->instanceid, $userid)) {
				$editlink =
						'<a href="'.get_update_version_url($version->get_id(), $version->get_instanceid()).'">'.
							'Edit'.
						'</a>';
			}

			if (sequentialdocuments_has_feedback_creation_rights($this->instanceid, $userid)) {
				$postfeedbacklink =
						'<a href="'.get_add_feedback_url($version->get_id(), $version->get_instanceid()).'">'.
							'Post a feedback'.
						'</a> ';
			}
        }

        $versionid = $version->get_id();
        $lastversionattr = '';
        if ($this->is_last_version($documentmanager, $version)) {
            $document = $documentmanager->get_document($version->get_documentid());
            if ($documentmanager->is_last_document($document)) {
                $lastversionattr = 'data-sqds-is-last';
            }
        }

        return  '<section class="sqds-version">'.
                    '<a onclick="toggle_visibility(\'sqds-version-content-'.$versionid.'\')">'.
                        'Version: '.$version->get_versionindice().
                    '</a>'.
                    '<div id="sqds-version-content-'.$versionid.
                    '" class="sqds-version-content" '.$lastversionattr.'>'.
                        $version->get_html().'<br />'.$filelist.$html.
                        '<aside class="sqds-bottom-right">'.
                            $postfeedbacklink.
                            $editlink.
                        '</aside>'.
                    '</div>'.
                '</section>';
    }
}


