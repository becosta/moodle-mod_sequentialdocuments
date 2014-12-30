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
include_once __DIR__.'/../dao/interaction_dao.php';
include_once __DIR__.'/../entity/entity.php';

interface interaction_visitor {
    public function visit_interaction(interaction $interaction);
    public function visit_added_document(added_document $interaction);
    public function visit_added_version(added_version $interaction);
    public function visit_added_feedback(added_feedback $interaction);
}

class history_interaction_visitor implements interaction_visitor {

    public function visit_interaction(interaction $interaction) {
        return '<section class="sqds-interaction">'.$interaction->get_html().'</section>';
    }

    public function visit_added_document(added_document $interaction) {
        global $DB;
        $document = $DB->get_record(
                            'sequentialdocuments_document', array('id' => $interaction->get_documentid())
        );

        return '<section class="sqds-interaction-added-document">'.
                    '<div class="sqds-header">'.
                        $interaction->get_html().'<br />'.
                    '</div>'.
                    '<strong>'.
                        get_string('interacttitle', 'mod_sequentialdocuments').
                    '</strong>'.$document->title.'<br />'.
                    '<strong>'.
                        get_string('interactdescription', 'mod_sequentialdocuments').
                    '</strong>'.$document->description.
                '</section>';
    }

    public function visit_added_version(added_version $interaction) {
        global $DB;
        $version = $DB->get_record('sequentialdocuments_version', array('id' => $interaction->get_versionid()));

        return '<section class="sqds-interaction-added-version">'.
                    '<div class="sqds-header">'.
                        $interaction->get_html().
                    '</div>'.
                '</section>';
    }

    public function visit_added_feedback(added_feedback $interaction) {
        global $DB;
        $feedback = $DB->get_record(
                            'sequentialdocuments_feedback', array('id' => $interaction->get_feedbackid())
        );

        return '<section class="sqds-interaction-added-feedback">'.
                    '<div class="sqds-header">'.
                        $interaction->get_html().'<br />'.
                    '</div>'.
                    $feedback->content.
                '</section>';
    }
}

class interaction_manager extends manager {

    const ENTITY_NAME = 'interaction';

    protected $added_documentdao;
    protected $added_versiondao;
    protected $added_feedbackdao;
    protected $interactionvisitor;

    public function __construct(array $data = null) {
        parent::__construct($data);
        $this->added_documentdao = new added_document_interaction_dao();
        $this->added_versiondao = new added_version_interaction_dao();
        $this->added_feedbackdao = new added_feedback_interaction_dao();
        $this->interactionvisitor = new history_interaction_visitor();
    }

    public function track_action_add_document($instanceid, $userid, $documentid) {
        $interaction = new added_document();
        $interaction->set_instanceid($instanceid);
        $interaction->set_userid($userid);
        $interaction->set_documentid($documentid);
        $interaction->set_date(time());
        $this->added_documentdao->insert($interaction);
    }

    public function track_action_delete_document($instanceid, $userid, $documentid) {
        $interaction = $this->added_documentdao->get_entity_where(array('documentid' => $documentid));
        if ($interaction) {
            $this->delete($interaction);
        }
    }

    public function track_action_add_version($instanceid, $userid, $versionid) {
        $interaction = new added_version();
        $interaction->set_instanceid($instanceid);
        $interaction->set_userid($userid);
        $interaction->set_versionid($versionid);
        $interaction->set_date(time());
        $this->added_versiondao->insert($interaction);
    }

    public function track_action_delete_version($instanceid, $userid, $versionid) {
        $interaction = $this->added_versiondao->get_entity_where(array('versionid' => $versionid));
        if ($interaction) {
            $this->added_versiondao->delete($interaction);
        }
    }

    public function track_action_add_feedback($instanceid, $userid, $feedbackid) {
        $interaction = new added_feedback();
        $interaction->set_instanceid($instanceid);
        $interaction->set_userid($userid);
        $interaction->set_feedbackid($feedbackid);
        $interaction->set_date(time());
        $this->added_feedbackdao->insert($interaction);
    }

    public function track_action_delete_feedback($instanceid, $userid, $feedbackid) {
        $interaction = $this->added_feedbackdao->get_entity_where(array('feedbackid' => $feedbackid));
        if ($interaction) {
            $this->added_feedbackdao->delete($interaction);
        }
    }


    public function get_interaction_html_by_id($id) {
        return $this->get_interaction_html_by_interaction_instance($this->get_entity($id));
    }

    public function get_interaction_html_by_interaction_instance(interaction $interaction) {
        return $interaction->accept($this->interactionvisitor);
    }

    public function get_entity($id) {
        return $this->call_on_each_dao('get_entity', $conditions);
    }

    public function get_entity_where(array $conditions) {
        return $this->call_on_each_dao('get_entity_where', $conditions, false);
    }

    public function get_entities_by_instanceid($instanceid) {
        return $this->get_all_entities_where(array('instanceid' => $instanceid));
    }

    public function get_all_entities_where(array $conditions) {
        return $this->call_on_each_dao('get_all_entities_where', $conditions, false);
    }

    public function insert(interaction $interaction) {
        return $this->call_on_each_dao('insert', $interaction);
    }

    public function update(interaction $interaction) {
        $this->call_on_each_dao('update', $interaction);
    }

    public function delete(interaction $interaction) {
        $this->call_on_each_dao('delete', $interaction);
    }

    protected function call_on_each_dao($method, $param, $untiltrue = true) {
        $vars = get_object_vars($this);
        $result = array();

        foreach ($vars as $dao) {

            if ($dao instanceof interaction_dao && is_callable(array($dao, $method))){
                $result[] = $dao->$method($param);
            }

            if ($untiltrue && end($result) !== false) {
                reset($result);
                break;
            }
        }
        return $result;
    }
}


