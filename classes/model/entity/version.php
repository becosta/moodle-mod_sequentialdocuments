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

class version extends entity {

    protected $documentid = -1;
    protected $versionindice = -1;
    protected $creationtime = -1;
    protected $fileslocation = array();

    protected function hydrate(array $data) {

        if (!isset($data['documentid'])) {
            throw new BadMethodCallException('Mising documentid parameter');
        }
        if (!isset($data['versionindice'])) {
            throw new BadMethodCallException('Mising versionindice parameter');
        }
        if (!isset($data['creationtime'])) {
            throw new BadMethodCallException('Mising creationtime parameter');
        }
        if (!isset($data['fileslocation'])) {
            throw new BadMethodCallException('Mising fileslocation parameter');
        }

        parent::hydrate($data);

        if (is_string($this->fileslocation)) {
            $this->fileslocation = unserialize($this->fileslocation);
        }
    }

    public function to_public_array() {
        $this->fileslocation = serialize($this->fileslocation);
        $arr = get_object_vars($this);
        $this->fileslocation = unserialize($this->fileslocation);
        return $arr;
    }

    public function get_html() {
        return '';
    }

    public function get_documentid() {
        return $this->documentid;
    }

    public function get_versionindice() {
        return $this->versionindice;
    }

    public function get_creationtime() {
        return $this->creationtime;
    }

    public function get_fileslocation() {
        return $this->fileslocation;
    }

    public function set_documentid($id) {
        $this->documentid = $this->check_numeric_id($id);
    }

    public function set_versionindice($i) {
        $this->versionindice = $this->check_numeric_id($i);
    }

    public function set_creationtime($timestamp) {
        if(!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->creationtime = $timestamp;
    }

    public function set_fileslocation($files) {
        $this->fileslocation = $files;
    }
}


