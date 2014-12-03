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
include_once __DIR__.'/../../../locallib.php';

class document extends entity {

    protected $authorid = -1;
    protected $title = '';
    protected $description = '';
    protected $documentindice = -1;
    protected $currentversionid = -1;
    protected $creationtime = -1;
    protected $modificationtime = -1;
    protected $locked = 0;

    protected function hydrate(array $data) {

        if (!isset($data['authorid'])) {
            throw new BadMethodCallException('Missing authorid parameter');
        }
        if (!isset($data['title'])) {
            throw new BadMethodCallException('Missing title parameter');
        }
        if (!isset($data['documentindice'])) {
            throw new BadMethodCallException('Missing documentindice parameter');
        }
        if (!isset($data['currentversionid'])) {
            throw new BadMethodCallException('Missing currentversionid parameter');
        }
        if (!isset($data['creationtime'])) {
            throw new BadMethodCallException('Missing creationtime parameter');
        }
        if (!isset($data['modificationtime'])) {
            throw new BadMethodCallException('Missing modificationtime parameter');
        }

        parent::hydrate($data);
    }

    public function get_html() {
        return  '<aside class="sqds-top-right">'.
                    '<strong>Created:</strong> '.userdate($this->creationtime).'<br />'.
                    '<strong>Modified:</strong> '.userdate($this->modificationtime).'<br />'.
                '</aside>'.
                '<strong>Title:</strong> '.
                '<a href="'.get_view_document_url($this->id, $this->instanceid).'">'.
                    '<h5>'.$this->title.'</h5>'.
                '</a><br />'.
                '<p class="sqds-description"><strong>Description:</strong> '.$this->description.'</p>'.
                '<strong>Content:</strong>';
    }

    public function get_authorid() {
        return $this->authorid;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_description() {
        return $this->description;
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

    public function is_locked() {
        return $this->locked;
    }

    public function set_authorid($id) {
        $this->authorid = $this->check_numeric_id($id);
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
        $this->documentindice = $this->check_numeric_id($i);
    }

    public function set_currentversionid($id) {
        $this->currentversionid = $this->check_numeric_id($id);
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

    public function set_locked($bool) {
        if ($bool) {
            $this->locked = 1;
        } else {
            $this->locked = 0;
        }
    }
}


