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

include_once __DIR__.'/interaction.php';

class added_version extends interaction {

    protected $versionid = -1;

    protected function hydrate(array $data) {

        if (!isset($data['versionid'])) {
            throw new BadMethodCallException('Missing versionid parameter');
        }

        parent::hydrate($data);
    }

    public function accept(interaction_visitor $visitor) {
        return $visitor->visit_added_version($this);
    }

    public function get_html() {
        return get_string('interactnewversion', 'mod_sequentialdocuments').parent::get_html();

    }

    public function get_versionid() {
        return $this->versionid;
    }

    public function set_versionid($id) {
        $this->versionid = $this->check_numeric_id($id);
    }
}


