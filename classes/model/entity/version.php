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

class version extends entity {

    protected $documentid = 0;
    protected $versionindice = 0;
    protected $creationtime = 0;
    protected $fileslocation = array();

    public function hydrate(array $data) {
        parent::hydrate($data);
    }

    public function get_html() {
        return '';
    }

    public function get_documentid() {

    }

    public function get_versionindice() {

    }

    public function get_creationtime() {

    }

    public function get_fileslocation() {

    }

    protected function set_documentid($id) {

    }

    protected function set_versionindice($i) {

    }

    protected function set_creationtime($date) {

    }

    protected function set_fileslocation(array $files) {

    }
}


