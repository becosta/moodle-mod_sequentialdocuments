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

abstract class entity {

    protected $id = 0;
    protected $instanceid = 0;

    public abstract function get_html();

    public function __construct(array $data = null) {
        if ($data !== null) {
            $this->hydrate($data);
        }
    }

    protected function hydrate(array $data) {

    }

    public function get_id() {

    }

    public function get_instance_id() {

    }

    protected function set_id($id) {

    }

    protected function set_instanceid($id) {
        
    }
}


