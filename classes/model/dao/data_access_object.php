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

abstract class data_access_object {

    protected $instanceid = 0;

    abstract protected function get_getentity_method();

    abstract protected function get_getentitywhere_method();

    abstract protected function get_getallentitieswhere_method();

    abstract protected function get_insert_method();

    abstract protected function get_update_method();

    abstract protected function get_delete_method();

    public function __construct($instanceid) {
        if (!is_int($instanceid) || !($instanceid > 0)) {
            throw new InvalidArgumentException();
        }
        $this->instanceid = $instanceid;
    }

    public function get_entity($id) {

    }

    public function get_entity_where(array $condition) {

    }

    public function get_all_entities_where(array $condition) {

    }

    public function insert(entity $entity) {

    }

    public function update(entity $entity) {

    }

    public function delete(entity $entity) {

    }
}


