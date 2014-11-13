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

    }

    public function get_authorid() {

    }

    public function get_title() {

    }

    public function get_documentindice() {

    }

    public function get_currentversionid() {

    }

    public function get_creationtime() {

    }

    public function get_modificationtime() {

    }

    protected function set_authorid($id) {

    }

    protected function set_title(string $title) {

    }

    protected function set_documentindice($i) {

    }

    protected function set_currentversionid($id) {

    }

    protected function set_creationtime($date) {

    }

    protected function set_modificationtime($date) {

    }
}


