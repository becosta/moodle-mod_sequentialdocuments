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

class feedback extends entity {

    protected $versionid = -1;
    protected $authorid = -1;
    protected $content = '';
    protected $fileslocation = array();

    public function hydrate(array $data) {

        if (!isset($data['versionid'])) {
            throw new BadMethodCallException('Missing versionid parameter');
        }
        if (!isset($data['authorid'])) {
            throw new BadMethodCallException('Missing authorid parameter');
        }
        if (!isset($data['content'])) {
            throw new BadMethodCallException('Missing content parameter');
        }
        if (!isset($data['fileslocation'])) {
            throw new BadMethodCallException('Missing fileslocation parameter');
        }

        parent::hydrate($data);
    }

    public function get_html() {
        return '';
    }

    public function get_versionid() {
        return $this->versionid;
    }

    public function get_authorid() {
        return $this->authorid;
    }

    public function get_content() {
        return $this->content;
    }

    public function get_fileslocation() {
        return $this->fileslocation;
    }

    protected function set_versionid($id) {
        $this->check_numeric_id($id);
        $this->versionid = $id;
    }

    protected function set_authorid($id) {
        $this->check_numeric_id($id);
        $this->authorid = $id;
    }

    protected function set_content(string $content) {
        $this->content = $content;
    }

    protected function set_fileslocation(array $files) {
        $this->fileslocation = $files;
    }
}


