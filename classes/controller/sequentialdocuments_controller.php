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

class sequentialdocuments_controller {

    protected $instanceid = 0;
    protected $courseid = 0;
    protected $userid = 0;
    protected $interactiondao = null;
    protected $documentdao = null;
    protected $versiondao = null;
    protected $feedbackdao = null;

    public function __construct($instanceid, $courseid, $userid) {

    }

    public function action_index(array $params = null) {

    }

    public function action_history(array $params = null) {

    }

    public function action_view_document(array $params = null) {

    }

    public function action_add_document(array $params = null) {

    }

    public function action_update_document(array $params = null) {

    }

    public function action_delete_document(array $params = null) {

    }

    public function action_add_feedback(array $params = null) {

    }

    public function action_edit_feedback(array $params = null) {

    }

    public function action_delete_feedback(array $params = null) {

    }

    protected function get_interactiondao() {

    }

    protected function get_documentdao() {

    }

    protected function get_versiondao() {

    }

    protected function get_feedbackdao() {

    }

    protected function set_instanceid($id) {

    }

    protected function set_courseid($id) {

    }

    protected function set_userid($id) {

    }
}


