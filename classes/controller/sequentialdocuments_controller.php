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
 *
 * @package    mod_sequentialdocuments
 * @author     Benjamin Costa
 * @copyright  (c) 2014 CNAM, Paris, France
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

include_once __DIR__.'/../model/manager/interaction_manager.php';
include_once __DIR__.'/../model/manager/document_manager.php';
include_once __DIR__.'/../model/manager/version_manager.php';
include_once __DIR__.'/../model/manager/feedback_manager.php';
include_once __DIR__.'/../model/manager/reminder_manager.php';
include_once __DIR__.'/../model/dao/document_dao.php';
include_once __DIR__.'/../model/dao/version_dao.php';
include_once __DIR__.'/../model/dao/feedback_dao.php';
include_once __DIR__.'/../model/dao/reminder_dao.php';
include_once __DIR__.'/../model/entity/entity.php';
include_once __DIR__.'/../view/index_view.php';
include_once __DIR__.'/../view/history_view.php';
include_once __DIR__.'/../view/form_view.php';

class sequentialdocuments_controller {

    protected $instance = null;

    protected $cm = null;

    /** @var int Sequencial Documents instance id we are currently manipulating */
    protected $instanceid = 0;

    /** @var int Course id this Sequencial Documents instance belong to */
    protected $courseid = 0;

    /** @var int Id of the user who is calling this script */
    protected $userid = 0;

    protected $contextid = -1;

    /** @var interactionmanager Interaction entities manager */
    protected $interactionmanager = null;

    /** @var documentmanager Document entities manager */
    protected $documentmanager = null;

    /** @var versionmanager Version entities manager */
    protected $versionmanager = null;

    /** @var feedbackmanager Feedback entities manager */
    protected $feedbackmanager = null;
    
    /** @var remindermanager Reminder entities manager */
    protected $remindermanager = null;

    /** @var documentdao Document entities data access object */
    protected $documentdao = null;

    /** @var versiondao Version entities data access object */
    protected $versiondao = null;

    /** @var feedbackdao Feedback entities data access object */
    protected $feedbackdao = null;

    /**
     * sequentialdocuments_controller constructor.
     *
     * @param int $instanceid Sequencial Documents instance id we are currently manipulating
     * @param int $courseid Course id this Sequencial Documents instance belong to
     * @param int $userid Id of the user who is calling this script
     */
    public function __construct($instance, $cm, $userid) {

        $this->instance = $instance;
        $this->cm = $cm;

        $this->set_instanceid((int)$instance->id);
        $this->set_courseid((int)$cm->id);
        $this->set_userid($userid);
        $this->contextid = context_module::instance($this->cm->id)->id;

        $this->interactionmanager = new interaction_manager();
        $this->documentmanager = new document_manager(array('instanceid' => $this->instanceid));
        $this->versionmanager = new version_manager(array('instanceid' => $this->instanceid));
        $this->feedbackmanager = new feedback_manager(array('instanceid' => $this->instanceid));
        $this->remindermanager = new reminder_manager(array('instanceid' => $this->instanceid));

        $this->documentdao = new document_dao();
        $this->versiondao = new version_dao();
        $this->feedbackdao = new feedback_dao();
    }

    public function action_error($errormessage) {
        $view = new index_view(array('instanceid' => $this->instanceid, 'title' => get_string('error', 'mod_sequentialdocuments'), 'intro' => ''));
        $view->set_content('<section class="sqds_error_message"><p>'.$errormessage.'</p></section>');
        $view->display();
        return;
    }

    public function action_unknown(array $params = null) {
        $this->action_error(get_string('pagenotfound', 'mod_sequentialdocuments'));
    }

    public function action_index(array $params = null) {

        $documents = $this->documentmanager->get_entities_by_instanceid($this->instanceid);
        $view = new index_view(
            array(
                'instanceid' => $this->instanceid,
                'title' => $this->instance->name,
                'intro' => $this->instance->intro,
            )
        );

        $content = '';
        try {
            if ($documents !== false) {
                foreach ($documents as $document) {
                    $content .= $this->documentmanager->
                                            get_document_html_by_document_instance(
                                                $document,
                                                $this->userid,
                                                $this->versionmanager,
                                                $this->feedbackmanager,
                                                $this->contextid
                                            )
                    ;
                }
            }
        } catch (unauthorized_access_exception $e) {
            $this->action_error(get_string('missingdocumentaccess', 'mod_sequentialdocuments'));
            return;
        }

        $view->set_content($content);
        $view->display();
    }

    public function action_edit_access(array $params = null) {

        if (!sequentialdocuments_has_edit_access_rights_rights($this->instanceid, $this->userid)) {
            $this->action_error(get_string('missingpageaccess', 'mod_sequentialdocuments'));
        }

        $onsubmit = function($formdata, $view) {
                        $formdata->id = $formdata->accessid;
                        $formdata->instanceid = $this->instanceid;
                        sequentialdocuments_update_current_access_rights($this->instanceid, (array)$formdata);
                        $this->action_index();
                    };

        $data = sequentialdocuments_get_current_access_rights($this->instanceid);
        $data['accessid'] = $data['id'];
        $this->form_based_action(
                'access_rights_config_form',
                'view.php?id='.$this->courseid.'&action=edit_access',
                $data,
                $onsubmit
        );
    }

    public function action_history(array $params = null) {

        $interactions = $this->interactionmanager->get_entities_by_instanceid($this->instanceid);
        $view = new history_view(
            array(
                'instanceid' => $this->instanceid,
                'title' => $this->instance->name,
                'intro' => $this->instance->intro,
            )
        );

        $content = '';
        if ($interactions !== false) {
            foreach ($interactions as $interactionarray) {
                if ($interactionarray !== false) {
                    foreach ($interactionarray as $interaction) {
                        $content .= $this->interactionmanager->
                                        get_interaction_html_by_interaction_instance($interaction);
                    }
                }
            }
        }

        $view->set_content($content);
        $view->display();
    }

    public function action_view_document(array $params = null) {

        $documentid = $this->get_numeric_id('documentid', $params);

        try {
            $content = $this->documentmanager->
                get_document_html_by_id(
                        $documentid,
                        $this->userid,
                        $this->versionmanager,
                        $this->feedbackmanager,
                        $this->contextid
                );
        } catch (unauthorized_access_exception $e) {
            $this->action_error(get_string('missingdocumentaccess', 'mod_sequentialdocuments'));
            die();
        }

        $view = new index_view(
            array(
                'instanceid' => $this->instanceid,
                'title' => $this->instance->name,
                'intro' => $this->instance->intro,
            )
        );
        $view->set_content($content);
        $view->display();
    }

    public function action_add_document(array $params = null) {

        if (!sequentialdocuments_has_document_creation_rights($this->instanceid, $this->userid)) {
            $this->action_error(get_string('missingdocumentcreationrights', 'mod_sequentialdocuments'));
        }

        $onsubmit = function($formdata, $view) {
                        $formdata->instanceid = $this->instanceid;
                        $formdata->authorid = $this->userid;
                        $formdata->description = $formdata->description['text'];
                        $id = $this->documentmanager->create_document($formdata, $this->versionmanager);
                        $this->interactionmanager->track_action_add_document($this->instanceid, (int)$this->userid, $id);
                        $this->action_view_document(array('documentid' => $id));
                    };

        $this->form_based_action(
                'add_document_form',
                'view.php?id='.$this->courseid.'&action=add_document',
                null,
                $onsubmit
        );
    }

    public function action_update_document(array $params = null) {

        if (!sequentialdocuments_has_document_edit_rights($this->instanceid, $this->userid)) {
            $this->action_error(get_string('missingdocumenteditionrights', 'mod_sequentialdocuments'));
        }

        $documentid = $this->get_numeric_id('documentid', $params);
        $document = $this->documentdao->get_entity($documentid);
        $data = array(
            'title' => $document->get_title(),
            'description' => array(
                'text' => $document->get_description()
            ),
        );

        $onsubmit = function($formdata, $view) use ($documentid) {
                        $formdata->instanceid = $this->instanceid;
                        $formdata->description = $formdata->description['text'];

                        try {
                            $this->documentmanager->update_document($documentid, $formdata);
                            $this->action_view_document(array('documentid' => $documentid));

                        } catch (InvalidArgumentException $e) {
                            $this->action_error(get_string('invaliddocumentid', 'mod_sequentialdocuments'));
                        }
                    };

        $this->form_based_action(
                'add_document_form',
                'view.php?id='.$this->courseid.'&action=update_document&documentid='.$documentid,
                $data,
                $onsubmit
        );
    }

    public function action_delete_document(array $params = null) {

        $documentid = $this->get_numeric_id('documentid', $params);

        try {
            $this->documentmanager->delete_document(
                                        $documentid,
                                        $this->userid,
                                        $this->versionmanager,
                                        $this->feedbackmanager
            );
            $this->interactionmanager->
                    track_action_delete_document($this->instanceid, $this->userid, $documentid);

        } catch (InvalidArgumentException $e) {
            $this->action_error(get_string('invaliddocumentid', 'mod_sequentialdocuments'));
        }

        $this->action_index($params);
    }

    public function action_lock_document(array $params = null) {

        $documentid = $this->get_numeric_id('documentid', $params);

        $this->documentmanager->lock_document(
            $documentid,
            $this->userid,
            $this->versionmanager,
            $this->feedbackmanager
        );
        $this->action_index($params);
    }

    public function action_unlock_document(array $params = null) {

        $documentid = $this->get_numeric_id('documentid', $params);

        $this->documentmanager->unlock_document(
                                    $documentid,
                                    $this->userid,
                                    $this->versionmanager,
                                    $this->feedbackmanager
        );
        $this->action_index($params);
    }

    public function action_view_version(array $params = null) {

        $versionid = $this->get_numeric_id('versionid', $params);

        $content = $this->versionmanager->get_version_html_by_id(
                                            $versionid,
                                            $this->userid,
                                            $this->documentmanager,
                                            $this->feedbackmanager,
                                            $this->contextid
        );

        $view = new index_view(
                        array(
                            'instanceid' => $this->instanceid,
                            'title' => $this->instance->name,
                            'intro' => $this->instance->intro,
                        )
        );
        $view->set_content($content);
        $view->display();
    }

    public function action_add_version(array $params = null) {

        if (!sequentialdocuments_has_version_creation_rights($this->instanceid, $this->userid)) {
            $this->action_error(get_string('missingversioncreationrights', 'mod_sequentialdocuments'));
        }

        $documentid = $this->get_numeric_id('documentid', $params);

        $data = array(
            'instanceid' => $this->instanceid,
            'dueday' => 1,
            'oneday' => 1,
            'oneweek' => 1,
            'twoweeks' => 0,
            'onemonth' => 0,
            'postneeded' => 0,
            'duetime' => 0,
        );

        $onsubmit = function($formdata, $view) use ($documentid) {
                        $formdata->instanceid = $this->instanceid;
                        $formdata->documentid = $documentid;

                        if (!isset($formdata->duetime) || $formdata->duetime == 0) {
                            $formdata->duetime = -1;
                        }

                        $id = $this->versionmanager->
                                create_version(
                                        $formdata,
                                        $this->contextid,
                                        $this->documentmanager,
                                        $this->feedbackmanager
                                );

                        $this->interactionmanager->
                                track_action_add_version($this->instanceid, (int)$this->userid, $id);

                        if ($formdata->duetime != -1) {
                            $formdata->versionid = $id;
                            $formdata->senderid = $this->userid;
                            $this->remindermanager->create_reminder($formdata);
                        }

                        $this->action_view_document(array('documentid' => $documentid));
                    };

        $this->form_based_action(
                'add_version_form',
                'view.php?id='.$this->courseid.'&action=add_version&documentid='.$documentid,
                $data,
                $onsubmit
        );
    }

    public function action_update_version(array $params = null) {

        $versionid = $this->get_numeric_id('versionid', $params);

        $hasrights = sequentialdocuments_has_version_edit_rights($this->instanceid, $this->userid);
        $isuser = sequentialdocuments_current_user_is_instance_student($this->instanceid);
        if (!$hasrights) {
            $version = $this->versionmanager->get_version($versionid);
            $needssubmission = $version->needs_user_submission();
        }

        if (!$hasrights && !($isuser && $needssubmission)) {
            $this->action_error(get_string('missingversioneditionrights', 'mod_sequentialdocuments'));
        }

        $duedate = $this->versionmanager->get_version($versionid)->get_duetime();
        if ($duedate == -1) {
            $duedate = 0;
        }

        $onsubmit = function($formdata, $view) use ($versionid, $isuser) {
                        $formdata->instanceid = $this->instanceid;
                        if (!isset($formdata->duetime) || $formdata->duetime == 0) {
                            $formdata->duetime = -1;
                        }
                        if ($isuser && $formdata->duetime != -1) {
                            $formdata->duevalidated = true;
                        }

                        try {
                            $this->versionmanager->update_version($versionid, $formdata, $this->contextid);

                            if (!$isuser && isset($formdata->duetime) && $formdata->duetime != 0) {

                                $reminder = $this->remindermanager->get_reminder_by_version_id($versionid);

                                if ($reminder == false) {
                                    $formdata->instanceid = $this->instanceid;
                                    $formdata->versionid = $versionid;
                                    $formdata->senderid = $this->userid;
                                    $reminder = $this->remindermanager->create_reminder($formdata);
                                }
                                $this->remindermanager->update_reminder_by_instance($reminder, $formdata);
                            }

                            $this->action_view_version(array('versionid' => $versionid));

                        } catch (InvalidArgumentException $e) {
                            $this->action_error(get_string('invaliddocumentid', 'mod_sequentialdocuments'));
                        }
                    };

        $onload =   function($form) use ($versionid) {
                        $form->set_data(
                                $this->versionmanager->get_entity_draft_area(
                                        $versionid, $this->contextid
                                )
                        );
                    };

        $data = $this->remindermanager->prepare_form_data(
                $this->remindermanager->get_reminder_by_version_id($versionid)
        );
        $data['instanceid'] = $this->instanceid;
        $data['duetime'] = $duedate;

        $this->form_based_action(
                'add_version_form',
                'view.php?id='.$this->courseid.'&action=update_version&versionid='.$versionid,
                $data,
                $onsubmit,
                $onload
        );
    }

    public function action_delete_version(array $params = null) {

        $versionid = $this->get_numeric_id('versionid', $params);

        try {
            $this->versionmanager->delete_version(
                                        $versionid,
                                        $this->userid,
                                        $this->documentmanager,
                                        $this->feedbackmanager
            );
            $this->interactionmanager->
                    track_action_delete_version($this->instanceid, $this->userid, $versionid);
        } catch (InvalidArgumentException $e) {
            $this->action_error(get_string('invalidversionid', 'mod_sequentialdocuments'));
        }

        $this->action_index($params);
    }

    public function action_lock_version(array $params = null) {

        $versionid = $this->get_numeric_id('versionid', $params);

        $this->versionmanager->lock($this->versionmanager->get_version($versionid), $this->userid);
        $this->action_index($params);
    }

    public function action_unlock_version(array $params = null) {

        $versionid = $this->get_numeric_id('versionid', $params);

        $this->versionmanager->unlock($this->versionmanager->get_version($versionid), $this->userid);
        $this->action_index($params);
    }

    public function action_view_feedback(array $params = null) {

        $feedbackid = $this->get_numeric_id('feedbackid', $params);

        $content = $this->feedbackmanager->get_feedback_html_by_id(
                                                $feedbackid,
                                                $this->userid,
                                                $this->documentmanager,
                                                $this->versionmanager,
                                                $this->contextid
        );

        $view = new index_view(
                        array(
                            'instanceid' => $this->instanceid,
                            'title' => $this->instance->name,
                            'intro' => $this->instance->intro,
                        )
        );
        $view->set_content($content);
        $view->display();
    }

    public function action_add_feedback(array $params = null) {

        if (!sequentialdocuments_has_feedback_creation_rights($this->instanceid, $this->userid)) {
            $this->action_error(get_string('missingfeedbackcreationrights', 'mod_sequentialdocuments'));
        }

        $versionid = $this->get_numeric_id('versionid', $params);

        $onsubmit = function($formdata, $view) use ($versionid) {
                        $formdata->instanceid = $this->instanceid;
                        $formdata->content = $formdata->content['text'];

                        if (isset($formdata->version) && $formdata->version != 0) {
                            $formdata->versionid = $formdata->version;
                        } else {
                            $formdata->versionid = $versionid;
                        }

                        $id = $this->feedbackmanager->create_feedback(
                                $formdata, $this->contextid, $this->versionmanager
                        );

                        $this->interactionmanager->
                                track_action_add_feedback($this->instanceid, (int)$this->userid, $id);

                        $this->action_view_feedback(array('feedbackid' => $id));
                    };

        $this->form_based_action(
                'add_feedback_form',
                'view.php?id='.$this->courseid.'&action=add_feedback&versionid='.$versionid,
                null,
                $onsubmit
        );
    }

    public function action_update_feedback(array $params = null) {

        if (!sequentialdocuments_has_feedback_edit_rights($this->instanceid, $this->userid)) {
            $this->action_error(get_string('missingfeedbackeditionrights', 'mod_sequentialdocuments'));
        }

        $feedbackid = $this->get_numeric_id('feedbackid', $params);
        $feedback = $this->feedbackmanager->get_feedback_by_id($feedbackid);
        $content = $feedback->get_content();
        $data = array('content' => array('text' => $content));

        $onload =   function($form) use ($feedback) {
                        $form->set_data(
                                $this->feedbackmanager->get_entity_draft_area(
                                        $feedback->get_id(), $this->contextid
                                )
                        );
                    }
        ;

        $onsubmit = function($formdata, $view) use ($feedbackid) {
                        $formdata->instanceid = $this->instanceid;
                        $formdata->content = $formdata->content['text'];

                        try {
                            $this->feedbackmanager->update_feedback($feedbackid, $formdata, $this->contextid);

                            $this->action_view_feedback(array('feedbackid' => $feedbackid));

                        } catch (InvalidArgumentException $e) {
                            $this->action_error(get_string('invalidfeedbackid', 'mod_sequentialdocuments'));
                        }
                    }
        ;

        $this->form_based_action(
                'add_feedback_form',
                'view.php?id='.$this->courseid.'&action=update_feedback&feedbackid='.$feedbackid,
                $data,
                $onsubmit,
                $onload
        );
    }

    public function action_delete_feedback(array $params = null) {
        $feedbackid = $this->get_numeric_id('feedbackid', $params);

        try {
            $this->feedbackmanager->delete_feedback($feedbackid, $this->userid);
            $this->interactionmanager->
                    track_action_delete_feedback($this->instanceid, $this->userid, $feedbackid);
        
        } catch (InvalidArgumentException $e) {
            $this->action_error(get_string('invalidfeedbackid', 'mod_sequentialdocuments'));
        }

        $this->action_index($params);
    }

    protected function form_based_action(
                                            $formclassname,
                                            $action = null,
                                            $customdata = null,
                                            $onsubmitcallback = null,
                                            $onloadcallback = null,
                                            $oncancellcallback = null,
                                            form_view $view = null) {

        include_once __DIR__.'/../view/form/'.$formclassname.'.php';
        $form = new $formclassname($action, $customdata);

        if ($view === null) {
            $view = new form_view(array('instanceid' => $this->instanceid, 'form' => $form));
        }

        $callback = null;
        $displayform = false;

        if ($form->is_cancelled()) {
            $callback = $oncancellcallback;

        } else if ($formdata = $form->get_data()) {
            $callback = $onsubmitcallback;

        } else {
            if (is_callable($onloadcallback)) {
                $onloadcallback($form);
            }
            $displayform = true;
        }

        if (is_callable($callback)) {
            $callback($form->get_data(), $view);
        }

        $view->display_header();
        $view->display_content($displayform);
        $view->display_footer();
    }

    protected function get_numeric_id($paramname, array $params) {
        if (!is_string($paramname) || !isset($params[$paramname])) {
            throw new BadMethodCallException();
        }
        return entity::check_numeric_id($params[$paramname]);
    }

    protected function get_interactionmanager() {
        return $this->interactionmanager;
    }

    protected function get_documentdao() {
        return $this->documentdao;
    }

    protected function get_versiondao() {
        return $this->versiondao;
    }

    protected function get_feedbackdao() {
        return $this->feedbackdao;
    }

    protected function set_instanceid($id) {
        entity::check_numeric_id($id);
        $this->instanceid = $id;
    }

    protected function set_courseid($id) {
        entity::check_numeric_id($id);
        $this->courseid = $id;
    }

    protected function set_userid($id) {
        entity::check_numeric_id($id);
        $this->userid = $id;
    }
}


