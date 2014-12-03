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
include_once __DIR__.'/../model/dao/document_dao.php';
include_once __DIR__.'/../model/dao/version_dao.php';
include_once __DIR__.'/../model/dao/feedback_dao.php';
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

        $this->documentdao = new document_dao();
        $this->versiondao = new version_dao();
        $this->feedbackdao = new feedback_dao();
    }

    public function action_error($errormessage) {
        $view = new index_view(array('instanceid' => $this->instanceid, 'title' => 'Error', 'intro' => ''));
        $view->set_content('<section class="sqds_error_message"><p>'.$errormessage.'</p></section>');
        $view->display();
    }

    public function action_unknown(array $params = null) {
        $this->action_error("Page not found");
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
        if ($documents !== false) {
            foreach ($documents as $document) {
                $content .= $this->documentmanager->
                                    get_document_html_by_document_instance(
                                        $document,
                                        $this->versionmanager,
                                        $this->feedbackmanager,
                                        $this->contextid
                                    )
                ;
            }
        }

        $view->set_content($content);
        $view->display();
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
                            $this->versionmanager,
                            $this->feedbackmanager,
                            $this->contextid
                    );
        } catch (unauthorized_access_exception $e) {
            $this->action_error('You don\'t have access to this document');
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

        $this->form_based_action(
                'add_document_form',
                'view.php?id='.$this->courseid.'&action=add_document',
                null,
                function($formdata, $view) {
                    $formdata->instanceid = $this->instanceid;
                    $formdata->authorid = $this->userid;
                    $id = $this->documentmanager->create_document($formdata, $this->versionmanager);
                    $this->interactionmanager->track_action_add_document($this->instanceid, (int)$this->userid, $id);
                    $this->action_view_document(array('documentid' => $id));
                }
        );
    }

    public function action_update_document(array $params = null) {

        $documentid = $this->get_numeric_id('documentid', $params);
        $document = $this->documentdao->get_entity($documentid);
        $data = array('title' => $document->get_title(), 'description' => $document->get_description());

        $this->form_based_action(
                'add_document_form',
                'view.php?id='.$this->courseid.'&action=update_document&documentid='.$documentid,
                $data,
                function($formdata, $view) use ($documentid) {

                    $formdata->instanceid = $this->instanceid;

                    try {
                        $this->documentmanager->update_document($documentid, $formdata);
                        $this->action_view_document(array('documentid' => $documentid));

                    } catch (unauthorized_access_exception $e) {
                        $this->action_error('You don\'t have access to this document');
                    } catch (InvalidArgumentException $e) {
                        $this->action_error('Invalid document id');
                    }
                }
        );
    }

    public function action_delete_document(array $params = null) {

        $documentid = $this->get_numeric_id('documentid', $params);

        try {
            $this->documentmanager->delete_document(
                                                $documentid,
                                                $this->versionmanager,
                                                $this->feedbackmanager
            );
        } catch (unauthorized_access_exception $e) {
            $this->action_error('You don\'t have access to this document');
        } catch (InvalidArgumentException $e) {
            $this->action_error('Invalid document id');
        }

        $this->action_index($params);
    }

    public function action_lock_document(array $params = null) {

        $documentid = $this->get_numeric_id('documentid', $params);

        $this->documentmanager->lock_document($documentid, $this->versionmanager, $this->feedbackmanager);
        $this->action_index($params);
    }

    public function action_unlock_document(array $params = null) {

        $documentid = $this->get_numeric_id('documentid', $params);

        $this->documentmanager->unlock_document($documentid, $this->versionmanager, $this->feedbackmanager);
        $this->action_index($params);
    }

    public function action_view_version(array $params = null) {

        $versionid = $this->get_numeric_id('versionid', $params);

        try {
            $content = $this->versionmanager->get_version_html_by_id(
                    $versionid, $this->documentmanager, $this->feedbackmanager, $this->contextid
            );
        } catch (unauthorized_access_exception $e) {
            $this->action_error('You don\'t have access to this document version');
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

    public function action_add_version(array $params = null) {

        $documentid = $this->get_numeric_id('documentid', $params);

        $this->form_based_action(
                'add_version_form',
                'view.php?id='.$this->courseid.'&action=add_version&documentid='.$documentid,
                null,
                function($formdata, $view) use ($documentid) {
                    $formdata->instanceid = $this->instanceid;
                    $formdata->documentid = $documentid;
                    $id = $this->versionmanager->
                            create_version($formdata, $this->documentmanager, $this->feedbackmanager);

                    file_save_draft_area_files(
                                        $formdata->attachments,
                                        $this->contextid,
                                        'mod_sequentialdocuments',
                                        'version',
                                        $id,
                                        array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 50)
                    );
                    $this->interactionmanager->
                            track_action_add_version($this->instanceid, (int)$this->userid, $id);

                    $this->action_view_document(array('documentid' => $documentid));
                }
        );
    }

    public function action_update_version(array $params = null) {

        $versionid = $this->get_numeric_id('versionid', $params);

        $this->form_based_action(
                'add_version_form',
                'view.php?id='.$this->courseid.'&action=update_version&versionid='.$versionid,
                null,
                function($formdata, $view) use ($versionid) {
                    $formdata->instanceid = $this->instanceid;

                    try {
                        $this->versionmanager->update_version($versionid, $formdata);

                        file_save_draft_area_files(
                                        $formdata->attachments,
                                        $this->contextid,
                                        'mod_sequentialdocuments',
                                        'version',
                                        $versionid,
                                        array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 50)
                        );

                        $this->action_view_version(array('versionid' => $versionid));

                    } catch (unauthorized_access_exception $e) {
                        $this->action_error('You don\'t have access to this document');
                    } catch (InvalidArgumentException $e) {
                        $this->action_error('Invalid document id');
                    }
                }
        );
    }

    public function action_delete_version(array $params = null) {

        $versionid = $this->get_numeric_id('versionid', $params);

        try {
            $this->versionmanager->delete_version($versionid, $this->documentmanager, $this->feedbackmanager);
        } catch (unauthorized_access_exception $e) {
            $this->action_error('You don\'t have access to this document version');
        } catch (InvalidArgumentException $e) {
            $this->action_error('Invalid version id');
        }

        $this->action_index($params);
    }

    public function action_view_feedback(array $params = null) {

        $feedbackid = $this->get_numeric_id('feedbackid', $params);

        try {
            $content = $this->feedbackmanager->get_feedback_html_by_id(
                    $feedbackid, $this->documentmanager, $this->versionmanager, $this->contextid
            );
        } catch (unauthorized_access_exception $e) {
            $this->action_error('You don\'t have access to this feedback version');
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

    public function action_add_feedback(array $params = null) {

        $versionid = $this->get_numeric_id('versionid', $params);

        $this->form_based_action(
                'add_feedback_form',
                'view.php?id='.$this->courseid.'&action=add_feedback&versionid='.$versionid,
                null,
                function($formdata, $view) use ($versionid) {
                    $formdata->instanceid = $this->instanceid;

                    if (isset($formdata->version) && $formdata->version != 0) {
                        $formdata->versionid = $formdata->version;
                    } else {
                        $formdata->versionid = $versionid;
                    }

                    $id = $this->feedbackmanager->create_feedback($formdata, $this->versionmanager);

                    file_save_draft_area_files(
                                        $formdata->attachments,
                                        $this->contextid,
                                        'mod_sequentialdocuments',
                                        'feedback',
                                        $id,
                                        array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 50)
                    );
                    $this->interactionmanager->
                            track_action_add_feedback($this->instanceid, (int)$this->userid, $id);

                    $this->action_view_feedback(array('feedbackid' => $id));
                }
        );
    }

    public function action_update_feedback(array $params = null) {

        $feedbackid = $this->get_numeric_id('feedbackid', $params);
        $feedback = $this->feedbackmanager->get_feedback_by_id($feedbackid);
        $content = $feedback->get_content();
        $data = array('content' => $content);

        $onload =   function($form) use ($feedback) {
                        $entry = new stdClass();
                        $entry->id = null;

                        $draftitemid = file_get_submitted_draft_itemid('attachments');
                        file_prepare_draft_area(
                                            $draftitemid,
                                            $this->contextid,
                                            'mod_sequentialdocuments',
                                            'feedback',
                                            $feedback->get_id(),
                                            array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 50)
                        );

                        $entry->attachments = $draftitemid;
                        $form->set_data($entry);
                    }
        ;

        $onsubmit = function($formdata, $view) use ($feedbackid) {
                        $formdata->instanceid = $this->instanceid;

                        try {
                            $this->feedbackmanager->update_feedback($feedbackid, $formdata);

                            file_save_draft_area_files(
                                        $formdata->attachments,
                                        $this->contextid,
                                        'mod_sequentialdocuments',
                                        'feedback',
                                        $feedbackid,
                                        array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 50)
                            );

                            $this->action_view_feedback(array('feedbackid' => $feedbackid));

                        } catch (unauthorized_access_exception $e) {
                            $this->action_error('You don\'t have access to this document feedback');
                        } catch (InvalidArgumentException $e) {
                            $this->action_error('Invalid feedback id');
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
            $this->feedbackmanager->delete_feedback($feedbackid);
        } catch (unauthorized_access_exception $e) {
            $this->action_error('You don\'t have access to this document feedback');
        } catch (InvalidArgumentException $e) {
            $this->action_error('Invalid feedback id');
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


