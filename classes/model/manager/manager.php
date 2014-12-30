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

include_once __DIR__.'/../dao/data_access_object.php';
include_once __DIR__.'/../entity/entity.php';

abstract class manager {

    protected $instanceid = -1;
    protected $dao = null;

    public function __construct(array $data = null) {

        if (!defined('static::ENTITY_NAME')) {
            throw new Exception('Constant ENTITY_NAME is not defined on subclass '.get_class($this));
        }

        if ($data !== null) {
            $this->hydrate($data);
        }
    }

    protected function hydrate(array $data) {

        if (!isset($data['instanceid'])) {
            throw new BadMethodCallException('Missing instanceid parameter');
        }

        foreach ($data as $property => $value) {
            $setter = 'set_'.$property;
            if (is_callable(array($this, $setter))) {
                $this->$setter($value);
            }
        }
    }

    public function save_entity_draft_area_file($entityid, $contextid, $attachments) {
        file_save_draft_area_files(
                            $attachments,
                            $contextid,
                            'mod_sequentialdocuments',
                            static::ENTITY_NAME,
                            $entityid,
                            array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 50)
        );
    }

    public function get_entity_draft_area($entityid, $contextid) {
        $entry = new stdClass();
        $entry->id = null;

        $draftitemid = file_get_submitted_draft_itemid('attachments');
        file_prepare_draft_area(
                            $draftitemid,
                            $contextid,
                            'mod_sequentialdocuments',
                            static::ENTITY_NAME,
                            $entityid,
                            array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 50)
        );
        $entry->attachments = $draftitemid;

        return $entry;
    }

    public function get_entities_by_instanceid($instanceid) {
        return $this->dao->get_all_entities_where(array('instanceid' => $instanceid));
    }

    protected function get_entity_instance_from_stdClass($classname, stdClass $data) {
        if (!is_string($classname)) {
            throw new InvalidArgumentException();
        }
        if (!class_exists($classname) || !in_array('entity', class_parents($classname))) {
            throw new InvalidArgumentException('$classname argument must be a valid entity child');
        }

        $entity = new $classname();

        $vars = get_object_vars($data);
        foreach ($vars as $property => $value) {
            $setter = 'set_'.$property;
            if (is_callable(array($entity, $setter))) {
                $entity->$setter($value);
            }
        }

        return $entity;
    }

    protected function set_instanceid($instanceid) {
        $this->instanceid = entity::check_numeric_id($instanceid);
    }
}


