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
include_once __DIR__.'/../../../locallib.php';

class version extends entity {

    protected $documentid = -1;
    protected $versionindice = -1;
    protected $creationtime = -1;
    protected $duetime = -1;
    protected $duevalidated = 0;
    protected $locked = 0;

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

        parent::hydrate($data);
    }

    public function get_html() {
        // TODO : add a submited by property and print the user info here
        // TODO : call either the controller or lib.php to get a link to the current version

        return  '<aside class="sqds-top-right">'.
                    get_string('versionsubmitted', 'mod_sequentialdocuments').userdate($this->creationtime).
                '</aside>'.
                $this->get_duedate_html();
    }

    protected function get_duedate_html() {
        $duedate = '';
        if ($this->duetime != -1) {

            switch($this->get_duedate_priority()) {
                case 3:
                    $class = 'class="sqds-priority-low"';
                    break;
                case 2:
                    $class = 'class="sqds-priority-medium"';
                    break;
                case 1:
                    $class = 'class="sqds-priority-high"';
                    break;
                default:
                    $class = '';
                    break;
            }

            $duedate = '<strong '.$class.'>'.
                            get_string('versionduedate', 'mod_sequentialdocuments').
                        '</strong> '.userdate($this->duetime).'<br />';
        }
        return $duedate;
    }

    public function get_duedate_priority() {

    if ($this->get_duevalidated() || $this->duetime == -1) {
        return 0;
    }

    // Number of days between $timestamp and current date
    $diff = ~floor((time() - $this->duetime) / 86400);

    if ($diff > 15) {
        $priority = 3;
    } else if ($diff > 2) {
        $priority = 2;
    } else {
        $priority = 1;
    }

    return $priority;
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

    public function get_duetime() {
        return $this->duetime;
    }

    public function get_duevalidated() {
        if ($this->duevalidated == 1) {
            return true;
        }
        return false;
    }

    public function is_locked() {
        if ($this->locked == 1) {
            return true;
        }
        return false;
    }

    public function needs_user_submission() {
        return $this->duetime != -1 && !$this->duevalidated;
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

    public function set_duetime($timestamp) {
        if (!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->duetime = $timestamp;
    }

    public function set_duevalidated($bool) {
        if ($bool) {
            $this->duevalidated = 1;
        } else {
            $this->duevalidated = 0;
        }
    }

    public function set_locked($bool) {
        if ($bool) {
            $this->locked = 1;
        } else {
            $this->locked = 0;
        }
    }
}


