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

        $this->set_documentid($data['documentid']);
        $this->set_versionindice($data['versionindice']);
        $this->set_creationtime($data['creationtime']);
        $this->set_fileslocation($data['fileslocation']);
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

    protected function set_documentid($id) {
        $this->check_numeric_id($id);
        $this->documentid = $id;
    }

    protected function set_versionindice($i) {
        if (!is_int($$i)) {
            throw new InvalidArgumentException();
        } else if ($i < 1) {
            throw new BadFunctionCallException(
                    'Received invalid versionindice parameter: "'.$i.
                    '". Version indice should be a positive integer.'
            );
        }
        $this->versionindice = $i;
    }

    protected function set_creationtime($timestamp) {
        if(!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->creationtime = $timestamp;
    }

    protected function set_fileslocation(array $files) {
        $this->fileslocation = $files;
    }
}


