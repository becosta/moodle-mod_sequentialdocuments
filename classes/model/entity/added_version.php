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

class added_version extends interaction {

    protected $versionid = 0;

    protected function hydrate(array $data) {
        parent::hydrate($data);

        if (!isset($data['versionid'])) {
            throw new BadMethodCallException('Missing versionid parameter');
        }

        $this->set_versionid($data['versionid']);
    }

    public function get_html() {
        return '';
    }

    public function get_versionid() {
        return $this->versionid;
    }

    protected function set_versionid($id) {
        $this->check_numeric_id($id);
        $this->versionid = $id;
    }
}


