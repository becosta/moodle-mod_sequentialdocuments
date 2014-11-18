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

include_once __DIR__.'/../entity/entity.php';

abstract class data_access_object {

    protected $instanceid = -1;

    public function __construct($instanceid, array $data = null) {

        if (!defined('static::ENTITY_TABLE')) {
            throw new Exception('Constant ENTITY_TABLE is not defined on subclass '.get_class($this));
        }

        if (!defined('static::ENTITY_CLASS_NAME')) {
            throw new Exception('Constant ENTITY_CLASS_NAME is not defined on subclass '.get_class($this));
        }

        entity::check_numeric_id($instanceid);
        $this->instanceid = $instanceid;

        if ($data !== null) {
            $this->hydrate($date);
        }
    }

    protected function hydrate() {
        foreach ($data as $property => $value) {
            $setter = 'set_'.$property;
            if (is_callable(array($this, $setter))) {
                $this->$setter($value);
            }
        }
    }

    public function get_entity($id) {
        entity::check_numeric_id($id);
        return $this->read_access('get_getentity_method', array('id' => $id));
    }

    public function get_entity_where(array $conditions) {
        return $this->read_access('get_getentitywhere_method', $conditions);
    }

    public function get_all_entities_where(array $conditions) {
        return $this->read_access('get_getallentitieswhere_method', $conditions);
    }

    public function insert(entity $entity) {
        $this->write_access('get_insert_method', $entity);
    }

    public function update(entity $entity) {
        $this->write_access('get_update_method', $entity);
    }

    public function delete(entity $entity) {
        $this->write_access('get_delete_method', $entity);
    }

    protected function read_access($method_getter, array $conditions) {

        if (!is_callable(array($this, $method_getter))) {
            throw new InvalidArgumentException();
        }

        if ($conditions === null) {
            throw new InvalidArgumentException();
        }

        global $DB;
        try {

            $lambda = $this->$method_getter($DB, $conditions);
            $this->check_lambda_method($lambda);

            $result = $lambda();

        } catch (dml_exception $e) {
            return false;
        }
        return $result;
    }

    protected function write_access($method_getter, entity $entity) {

        if (!is_callable(array($this, $method_getter))) {
            throw new InvalidArgumentException();
        }

        if ($entity === null) {
            throw new InvalidArgumentException();
        }

        global $DB;
        try {

            $lambda = $this->$method_getter($DB, $entity);
            $this->check_lambda_method($lambda);

            return $lambda();

        } catch (dml_exception $e) {
            return false;
        }
    }

    protected function get_getentity_method($DB, array $id) {
        return $this->get_getentitywhere_method($DB, $id);
    }

    protected function get_getentitywhere_method($DB, array $conditions) {
        $table = static::ENTITY_TABLE;
        $class = static::ENTITY_CLASS_NAME;

        return function() use ($DB, $conditions, $table, $class) {
            $record = $DB->get_records($table, $conditions);
            $data = current($record);

            if ($data === false || count($record != 1)) {
                return false;
            }
            return new $class((array)$data);
        };
    }

    protected function get_getallentitieswhere_method($DB, array $conditions) {
        $table = static::ENTITY_TABLE;
        $class = static::ENTITY_CLASS_NAME;

        return function() use ($DB, $conditions, $table, $class) {
            $records = $DB->get_records($table, $conditions);
            $count = count($records);
            $entities = array();

            if ($count == 0 || ($count == 1 && current($records) === false)) {
                return false;
            }

            foreach ($records as $record) {
                if ($record !== false) {
                    $entities[] = new $class((array)$record);
                }
            }
            return $entities;
        };
    }

    protected function get_insert_method($DB, $entity) {
        $this->check_entity_class($entity, static::ENTITY_CLASS_NAME);
        $table = static::ENTITY_TABLE;

        return function() use ($DB, $entity, $table) {
            return $DB->insert_record($table, $entity->to_public_stdClass());
        };
    }

    protected function get_update_method($DB, $entity) {
        $this->check_entity_class($entity, static::ENTITY_CLASS_NAME);
        $table = static::ENTITY_TABLE;

        return function() use ($DB, $entity, $table) {
            return $DB->update_record($table, $entity->to_public_stdClass());
        };
    }

    protected function get_delete_method($DB, $entity) {
        $this->check_entity_class($entity, static::ENTITY_CLASS_NAME);
        $table = static::ENTITY_TABLE;
        $condition = array('id' => $entity->get_id());

        return function() use ($DB, $table, $condition) {
            return $DB->delete_records($table, $condition);
        };
    }

    protected function check_lambda_method($lambda) {
        if (!is_callable($lambda)) {
            throw new LogicException(
                    'Bad data_access_object specialization, '.
                    '"get_getentity_method" should return a lambda function'
            );
        }
    }

    protected function check_entity_class($entity, $classname) {
        if (!is_a($entity, $classname)) {
            throw new InvalidArgumentException();
        }
    }
}


