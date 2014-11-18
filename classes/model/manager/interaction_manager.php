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

include_once __DIR__.'/../dao/interaction_dao.php';
include_once __DIR__.'/../entity/entity.php';

class interaction_manager {

    protected $instanceid;
    protected $added_documentdao;
    protected $added_versiondao;
    protected $added_feedbackdao;

    public function __construct($instanceid) {

        entity::check_numeric_id($instanceid);
        $this->instanceid = $instanceid;

        $this->added_document_dao = new added_document_interaction_dao($instanceid);
        $this->added_version_dao = new added_version_interaction_dao($instanceid);
        $this->added_feedback_dao = new added_feedback_interaction_dao($instanceid);
    }

    public function get_entity($id) {
        return $this->call_on_each_dao('get_entity', $conditions);
    }

    public function get_entity_where(array $conditions) {
        return $this->call_on_each_dao('get_entity_where', $conditions, false);
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

