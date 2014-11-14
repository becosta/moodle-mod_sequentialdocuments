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

    protected $instanceid = -1;

    abstract protected function get_getentity_method($DB, $instanceid, $id);

    abstract protected function get_getentitywhere_method($DB, $instanceid, array $conditions);

    abstract protected function get_getallentitieswhere_method($DB, $instanceid, array $conditions);

    abstract protected function get_insert_method($DB, $instanceid, $entity);

    abstract protected function get_update_method($DB, $instanceid, $entity);

    abstract protected function get_delete_method($DB, $instanceid, $entity);

    public function __construct($instanceid) {
        entity::check_numeric_id($id);
        $this->instanceid = $instanceid;
    }

    public function get_entity($id) {
        entity::check_numeric_id($id);

        global $DB;
        try {

            $lambda = $this->get_getentity_method();
            $this->check_lambda_method($lambda);

            $entity = $lambda($DB, $this->instanceid, $id);

        } catch (dml_exception $e) {
            return false;
        }
        return $entity;
    }

    public function get_entity_where(array $conditions) {
        if ($conditions === null) {
            throw new InvalidArgumentException();
        }

        global $DB;
        try {

            $lambda = $this->get_getentitywhere_method();
            $this->check_lambda_method($lambda);

            $entity = $lambda($DB, $this->instanceid, $conditions);

        } catch (dml_exception $e) {
            return false;
        }
        return $entity;
    }

    public function get_all_entities_where(array $conditions) {
        if ($conditions === null) {
            throw new InvalidArgumentException();
        }

        global $DB;
        try {

            $lambda = $this->get_getallentitieswhere_method();
            $this->check_lambda_method($lambda);

            $entities = $lambda($DB, $this->instanceid, $conditions);

        } catch (dml_exception $e) {
            return false;
        }
        return $entities;
    }

    public function insert(entity $entity) {
        if ($entity === null) {
            throw new InvalidArgumentException();
        }

        global $DB;
        try {

            $lambda = $this->get_insert_method();
            $this->check_lambda_method($lambda);

            return $lambda($DB, $this->instanceid, $entity);

        } catch (dml_exception $e) {
            return false;
        }
    }

    public function update(entity $entity) {
        if ($entity === null) {
            throw new InvalidArgumentException();
        }

        global $DB;
        try {

            $lamda = $this->get_update_method();
            $this->check_lambda_method($lambda);

            return $lamda($DB, $this->instanceid, $entity);

        } catch (dml_exception $e) {
            return false;
        }
    }

    public function delete(entity $entity) {
        if ($entity === null) {
            throw new InvalidArgumentException();
        }

        global $DB;
        try {

            $lambda = $this->get_delete_method();
            $this->check_lambda_method($lambda);

            return $lambda($DB, $this->instanceid, $entity);

        } catch (dml_exception $e) {
            return false;
        }
    }

    protected function check_lambda_method($lambda) {
        if (!is_callable($lambda)) {
            throw new LogicException(
                    'Bad data_access_object specialization, '.
                    '"get_getentity_method" should return a lambda function'
            );
        }
    }
}


