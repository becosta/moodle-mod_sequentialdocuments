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

class document extends entity {

    protected $authorid = 0;
    protected $title = '';
    protected $documentindice = 0;
    protected $currentversionid = 0;
    protected $creationtime = 0;
    protected $modificationtime = 0;

    protected function hydrate(array $data) {
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

    protected function set_authorid($id) {
        $this->check_numeric_id($id);
        $this->authorid = $id;
    }

    protected function set_title(string $title) {
        if (!is_string($title)) {
            throw new InvalidArgumentException();
        }
        $this->title = $title;
    }

    protected function set_documentindice($i) {
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

    protected function set_currentversionid($id) {
        $this->check_numeric_id($id);
        $this->currentversionid = $id;
    }

    protected function set_creationtime($timestamp) {
        if (!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->creationtime = $timestamp;
    }

    protected function set_modificationtime($timestamp) {
        if (!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->modificationtime = $timestamp;
    }
}


