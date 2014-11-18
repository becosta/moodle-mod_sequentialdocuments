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

abstract class interaction extends entity {

    protected $userid = -1;
    protected $date = -1;

    protected function hydrate(array $data) {

        if (!isset($data['userid'])) {
            throw new BadMethodCallException('Missing userid parameter');
        }
        if (!isset($data['date'])) {
            throw new BadMethodCallException('Missing date parameter');
        }

        parent::hydrate($data);
    }

    public function get_userid() {
        return $this->userid;
    }

    public function get_date() {
        return $this->date;
    }

    protected function set_userid($id) {
        $this->check_numeric_id($id);
        $this->id = $id;
    }

    protected function set_date($timestamp) {
        if(!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->date = $timestamp;
    }
}


