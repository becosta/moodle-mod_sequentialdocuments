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

class reminder extends entity {

    protected $versionid = -1;
    protected $senderid = -1;
    protected $onemonth = 0;
    protected $onemonthsent = 0;
    protected $twoweeks = 0;
    protected $twoweekssent = 0;
    protected $oneweek = 0;
    protected $oneweeksent = 0;
    protected $oneday = 0;
    protected $onedaysent = 0;
    protected $dueday = 0;
    protected $duedaysent = 0;
    protected $postneeded = 0;
    protected $postcount = 0;
    protected $lastpost = -1;
    protected $duetime = -1;

    public function get_html() {
        return '';
    }

    public function get_versionid() {
        return $this->versionid;
    }

    public function get_senderid() {
        return $this->senderid;
    }

    public function get_onemonth() {
        return $this->onemonth;
    }

    public function get_onemonthsent() {
        return $this->onemonthsent;
    }

    public function get_twoweeks() {
        return $this->twoweeks;
    }

    public function get_twoweekssent() {
        return $this->twoweekssent;
    }

    public function get_oneweek() {
        return $this->oneweek;
    }

    public function get_oneweeksent() {
        return $this->oneweeksent;
    }

    public function get_oneday() {
        return $this->oneday;
    }

    public function get_onedaysent() {
        return $this->onedaysent;
    }

    public function get_dueday() {
        return $this->dueday;
    }

    public function get_duedaysent() {
        return $this->duedaysent;
    }

    public function get_postneeded() {
        return $this->postneeded;
    }

    public function get_postcount() {
        return $this->postcount;
    }

    public function get_lastpost() {
        return $this->lastpost;
    }

    public function get_duetime() {
        return $this->duetime;
    }

    public function set_versionid($id) {
        $this->versionid = $this->check_numeric_id($id);
    }

    public function set_senderid($id) {
        $this->senderid = $this->check_numeric_id($id);
    }

    public function set_onemonth($bool) {
        if ($bool) {
            $this->onemonth = 1;
        } else {
            $this->onemonth = 0;
        }
    }

    public function set_onemonthsent($bool) {
        if ($bool) {
            $this->onemonthsent = 1;
        } else {
            $this->onemonthsent = 0;
        }
    }

    public function set_twoweeks($bool) {
        if ($bool) {
            $this->twoweeks = 1;
        } else {
            $this->twoweeks = 0;
        }
    }

    public function set_twoweekssent($bool) {
        if ($bool) {
            $this->twoweekssent = 1;
        } else {
            $this->twoweekssent = 0;
        }
    }

    public function set_oneweek($bool) {
        if ($bool) {
            $this->oneweek = 1;
        } else {
            $this->oneweek = 0;
        }
    }

    public function set_oneweeksent($bool) {
        if ($bool) {
            $this->oneweeksent = 1;
        } else {
            $this->oneweeksent = 0;
        }
    }

    public function set_oneday($bool) {
        if ($bool) {
            $this->oneday = 1;
        } else {
            $this->oneday = 0;
        }
    }

    public function set_onedaysent($bool) {
        if ($bool) {
            $this->onedaysent = 1;
        } else {
            $this->onedaysent = 0;
        }
    }

    public function set_dueday($bool) {
        if ($bool) {
            $this->dueday = 1;
        } else {
            $this->dueday = 0;
        }
    }

    public function set_duedaysent($bool) {
        if ($bool) {
            $this->duedaysent = 1;
        } else {
            $this->duedaysent = 0;
        }
    }

    public function set_postneeded($n) {
        $this->postneeded = $this->check_numeric_id($n);
    }

    public function set_postcount($count) {
        $this->postcount = $this->check_numeric_id($count);
    }

    public function set_lastpost($timestamp) {
        if (!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->duetime = $timestamp;
    }

    public function set_duetime($timestamp) {
        if (!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->duetime = $timestamp;
    }
}


