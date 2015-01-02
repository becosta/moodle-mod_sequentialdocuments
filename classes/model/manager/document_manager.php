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
include_once __DIR__.'/version_manager.php';
include_once __DIR__.'/feedback_manager.php';
include_once __DIR__.'/../dao/document_dao.php';
include_once __DIR__.'/../entity/document.php';
include_once __DIR__.'/../entity/version.php';
include_once __DIR__.'/../entity/feedback.php';
include_once __DIR__.'/../exception/unauthorized_access_exception.php';
include_once __DIR__.'/../../../locallib.php';
include_once __DIR__.'/../../controller/sequentialdocuments_controller.php';

class document_manager extends manager {

    const ENTITY_NAME = 'document';

    public function __construct(array $data = null) {
        parent::__construct($data);
        $this->dao = new document_dao();
    }

    public function create_document(stdClass $data, version_manager $versionmanager) {

        global $DB;

        $timestamp = time();
        $data->creationtime = $timestamp;
        $data->modificationtime = $timestamp;
        $data->currentversion = 1;

        $data->documentindice =
                $DB->count_records(
                    'sequentialdocuments_document',
                    array('instanceid' => $data->instanceid)
                ) + 1
        ;

        $document = $this->get_entity_instance_from_stdClass('document', $data);

        $documentid = $this->dao->insert($document);
        $data->documentid = $documentid;
        $data->timestamp = $timestamp;

        $document->set_id($documentid);
        $document->set_currentversionid($versionmanager->create_first_document_version($data));
        $this->dao->update($document);

        return $documentid;
    }

    public function update_document($documentid, stdClass $data) {

        global $DB;
        $document = $this->dao->get_entity($documentid);

        if (!($document instanceof document)) {
            throw new InvalidArgumentException();
        } else if ($document->get_instanceid() != $this->instanceid) {
            throw new unauthorized_access_exception('ua1000', 'mod_sequentialdocuments');
        }

        $vars = get_object_vars($data);
        foreach ($vars as $property => $value) {
            $setter = 'set_'.$property;
            if (is_callable(array($document, $setter))) {
                $document->$setter($value);
            }
        }
        $this->dao->update($document);
    }

    public function delete_document(
                                    $documentid,
                                    $userid,
                                    version_manager $versionmanager,
                                    feedback_manager $feedbackmanager) {

        $document = $this->dao->get_entity($documentid);

        if (!($document instanceof document)) {
            throw new InvalidArgumentException();
        } else if ($document->get_instanceid() != $this->instanceid) {
            throw new unauthorized_access_exception('uainstance', 'mod_sequentialdocuments');
        }

        if (!sequentialdocuments_has_document_suppression_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception('ua1001', 'mod_sequentialdocuments');
        }

        $versionmanager->delete_versions_by_documentid($documentid, $userid, $feedbackmanager);
        $this->dao->delete($document);
    }

    public function lock_document($documentid, $userid, version_manager $versionmanager, feedback_manager $feedbackmanager) {

        if (!sequentialdocuments_has_lock_document_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception('ua1004', 'mod_sequentialdocuments');
        }

        $document = $this->dao->get_entity($documentid);
        $document->set_locked(true);

        $versions = $versionmanager->get_versions_by_documentid($documentid);
        if ($versions !== false) {
            foreach ($versions as $version) {
                $versionmanager->lock($version, $userid);
                $feedbackmanager->lock_feedbacks_by_version($version, $userid);
            }
        }

        $this->dao->update($document);
    }

    public function set_new_document_version($documentid, $versionid) {
        $document = $this->dao->get_entity($documentid);
        if ($document === false) {
            return false;
        }

        $document->set_currentversionid($versionid);
        $this->dao->update($document);
    }

    public function notify_version_deletion(version_manager $versionmanager, version $version) {
        $previous = $versionmanager->get_previous_version($version);
        if ($previous) {
            $document = $this->dao->get_entity($version->get_documentid());
            $document->set_currentversionid($previous->get_id());
            $this->dao->update($document);
        }
        else {
            $data = new stdClass;
            $data->instanceid = $this->instanceid;
            $data->documentid = $version->get_documentid();
            $data->timestamp = time();
            $versionmanager->create_first_document_version($data);
        }
    }

    public function unlock_document(
                                    $documentid,
                                    $userid,
                                    version_manager $versionmanager,
                                    feedback_manager $feedbackmanager) {

        if (!sequentialdocuments_has_lock_document_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception('ua1004', 'mod_sequentialdocuments');
        }

        $document = $this->dao->get_entity($documentid);
        $document->set_locked(false);

        $version = $versionmanager->get_current_document_version($document);
        $versionmanager->unlock($version, $userid);

        $this->dao->update($document);
    }

    public function is_last_document(document $document) {
        global $DB;
        $count = $DB->count_records(
                    'sequentialdocuments_document',
                    array('instanceid' => $document->get_instanceid())
                )
        ;
        if ($document->get_documentindice() == $count) {
            return true;
        }
        return false;
    }

    public function get_document($id) {
        return $this->dao->get_entity($id);
    }

    public function get_document_html_by_id(
                                            $id,
                                            $userid,
                                            version_manager $versionmanager,
                                            feedback_manager $feedbackmanager,
                                            $contextid) {

        if (!sequentialdocuments_has_document_read_rights($this->instanceid, $userid)) {
            throw new unauthorized_access_exception('ua1000', 'mod_sequentialdocuments');
        }

        $document = $this->dao->get_entity($id);
        if ($document === false) {
            throw new inexistent_entity_exception('uedocument', 'mod_sequentialdocuments');
        }

        return $this->get_document_html_by_document_instance(
							$document,
							$userid,
							$versionmanager,
							$feedbackmanager,
							$contextid
        );
    }

    public function get_document_html_by_document_instance(
                                                    document $document,
                                                    $userid,
                                                    version_manager $versionmanager,
                                                    feedback_manager $feedbackmanager,
                                                    $contextid) {


        if ($document->get_instanceid() != $this->instanceid) {
            throw new unauthorized_access_exception('uainstance', 'mod_sequentialdocuments');
        }

        if (!sequentialdocuments_has_document_read_rights($this->instanceid, $userid)) {
            return '';
        }

        $lastversionattr = '';
        if ($this->is_last_document($document)) {
            $lastversionattr = 'data-sqds-is-last';
        }
        $documentid = $document->get_id();
        $html = '<a onclick="toggle_visibility(\'sqds-document-content-'.$documentid.'\')">'.
                    get_string('documentcontent', 'mod_sequentialdocuments').
                '</a>'.
                '<div id="sqds-document-content-'.$documentid.
                '" class="sqds-document-content" '.$lastversionattr.'>';
        $versions = $versionmanager->get_versions_by_documentid($documentid);
        if ($versions !== false) {
            foreach ($versions as $version) {
                $html .= $versionmanager->get_version_html_by_version_instance(
                                            $version, $userid, $this, $feedbackmanager, $contextid
                );
            }
        }
        $html .= '</div>';

        $links = '';
        if ($document->is_locked()) {
            if (sequentialdocuments_has_lock_document_rights($this->instanceid, $userid)) {
                $links =
                    '<a href="'.
                        get_unlock_document_url($document->get_id(), $this->instanceid).
                        '">'.get_string('documentunlocklink', 'mod_sequentialdocuments').
                    '</a> ';
            }
        } else {
            if (sequentialdocuments_has_lock_document_rights($this->instanceid, $userid)) {
                $links .=
                    '<a href="'.
                        get_lock_document_url($documentid, $this->instanceid).
                        '">'.get_string('documentlocklink', 'mod_sequentialdocuments').
                    '</a> ';
            }
            if (sequentialdocuments_has_version_creation_rights($this->instanceid, $userid)) {
                $links .=
                    '<a href="'.
                        get_add_version_url($documentid, $this->instanceid).
                        '">'.get_string('documentaddversionlink', 'mod_sequentialdocuments').
                    '</a> ';
            }
            if (sequentialdocuments_has_document_edit_rights($this->instanceid, $userid)) {
                $links .=
                    '<a href="'.
                        get_update_document_url($documentid, $this->instanceid).
                        '">'.get_string('documenteditlink', 'mod_sequentialdocuments').
                    '</a> ';
            }
            if (sequentialdocuments_has_document_suppression_rights($this->instanceid, $userid)) {
                $links .=
                    '<a href="'.
                        get_delete_document_url($documentid, $this->instanceid).
                        '" onclick="return confirm(\''.get_string('documentdeleteconfirmation', 'mod_sequentialdocuments').
                        '\');">'.
                        get_string('documentdeletelink', 'mod_sequentialdocuments').
                    '</a>';
            }
        }

        return '<section class="sqds-document">'.$document->get_html().$html.
                    '<aside class="sqds-bottom-right">'.
                        $links.
                    '</aside>'.
                '</section>';
    }
}


