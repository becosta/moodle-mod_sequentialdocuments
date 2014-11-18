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

include_once __DIR__.'/entity.php';

class document extends entity {

    protected $authorid = -1;
    protected $title = '';
    protected $description = '';
    protected $documentindice = -1;
    protected $currentversionid = -1;
    protected $creationtime = -1;
    protected $modificationtime = -1;

    protected function hydrate(array $data) {

        if (!is_set($data['authorid'])) {
            throw new BadMethodCallException('Missing authorid parameter');
        }
        if (!is_set($data['title'])) {
            throw new BadMethodCallException('Missing title parameter');
        }
        if (!is_set($data['documentindice'])) {
            throw new BadMethodCallException('Missing documentindice parameter');
        }
        if (!is_set($data['currentversionid'])) {
            throw new BadMethodCallException('Missing currentversionid parameter');
        }
        if (!is_set($data['creationtime'])) {
            throw new BadMethodCallException('Missing creationtime parameter');
        }
        if (!is_set($data['modificationtime'])) {
            throw new BadMethodCallException('Missing modificationtime parameter');
        }

        parent::hydrate($data);
    }

    public function get_html() {
        return '';
    }

    public function get_authorid() {
        return $this->authorid;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_description() {

    }

    public function get_documentindice() {
        return $this->documentindice;
    }

    public function get_currentversionid() {
        return $this->currentversionid;
    }

    public function get_creationtime() {
        return $this->creationtime;
    }

    public function get_modificationtime() {
        return $this->modificationtime;
    }

    public function set_authorid($id) {
        $this->check_numeric_id($id);
        $this->authorid = $id;
    }

    public function set_title($title) {
        if (!is_string($title)) {
            throw new InvalidArgumentException();
        }
        $this->title = $title;
    }

    public function set_description($description) {
        if (!is_string($description)) {
            throw new InvalidArgumentException();
        }
        $this->description = $description;
    }

    public function set_documentindice($i) {
        if (!is_int($i)) {
            throw new InvalidArgumentException();
        } else if ($i < 1) {
            throw new BadFunctionCallException(
                    'Received invalid documentindice parameter: "'.$i.
                    '". Document indice should be a positive integer.'
            );
        }
        $this->documentindice = $i;
    }

    public function set_currentversionid($id) {
        $this->check_numeric_id($id);
        $this->currentversionid = $id;
    }

    public function set_creationtime($timestamp) {
        if (!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->creationtime = $timestamp;
    }

    public function set_modificationtime($timestamp) {
        if (!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->modificationtime = $timestamp;
    }
}


