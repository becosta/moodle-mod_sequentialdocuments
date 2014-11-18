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

abstract class interaction_dao extends data_access_object {

    const ENTITY_TABLE = 'sequentialdocuments_interact';

    public function __construct($instanceid, array $data = null) {

        if (!defined('static::SPECIFIC_PROPERTY')) {
            throw new Exception('Constant SPECIFIC_PROPERTY is not defined on subclass '.get_class($this));
        }

        parent::__construct($instanceid, $data);
    }

    protected function get_getentitywhere_method($DB, array $conditions) {
        $table = static::ENTITY_TABLE;
        $specific = static::SPECIFIC_PROPERTY;
        $class = static::ENTITY_CLASS_NAME;

        return function() use ($DB, $conditions, $table, $specific, $class) {
            $record = $DB->get_records($table, $conditions);
            $data = current($record);

            if ($data === false || count($record != 1)) {
                return false;
            } else if ($data->$specific === null) {
                return false;
            }
            return new $class((array)$data);
        };
    }

    protected function get_getallentitieswhere_method($DB, array $conditions) {
        $table = static::ENTITY_TABLE;
        $specific = static::SPECIFIC_PROPERTY;
        $class = static::ENTITY_CLASS_NAME;

        return function() use ($DB, $conditions, $table, $specific, $class) {
            $records = $DB->get_records($table, $conditions);
            $count = count($records);
            $entities = array();

            if ($count == 0 || ($count == 1 && current($records) === false)) {
                return false;
            }

            foreach ($records as $record) {
                if ($record !== false && $record->$specific !== null) {
                    $entities[] = new $class((array)$record);
                }
            }
            return $entities;
        };
    }
}

class added_document_interaction_dao extends interaction_dao {
    const ENTITY_CLASS_NAME = 'added_document';
    const SPECIFIC_PROPERTY = 'documentid';
}

class added_version_interaction_dao extends interaction_dao {
    const ENTITY_CLASS_NAME = 'added_version';
    const SPECIFIC_PROPERTY = 'versionid';
}

class added_feedback_interaction_dao extends interaction_dao {
    const ENTITY_CLASS_NAME = 'added_feedback';
    const SPECIFIC_PROPERTY = 'feedbackid';
}


