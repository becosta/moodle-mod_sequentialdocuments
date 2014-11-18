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

class added_document extends interaction {

    protected $documentid = -1;

    protected function hydrate(array $data) {

        if (!isset($data['documentid'])) {
            throw new BadMethodCallException('Missing documentid parameter');
        }

        parent::hydrate($data);
    }

    public function get_html() {
        return '';
    }

    public function get_documentid() {
        return $this->documentid;
    }

    public function set_documentid($id) {
        $this->check_numeric_id($id);
        $this->documentid = $id;
    }
}


