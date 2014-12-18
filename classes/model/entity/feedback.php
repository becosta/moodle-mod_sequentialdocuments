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

class feedback extends entity {

    protected $versionid = -1;
    protected $authorid = -1;
    protected $feedbackindice = -1;
    protected $creationtime = -1;
    protected $content = '';
    protected $locked = 0;

    protected function hydrate(array $data) {

        if (!isset($data['versionid'])) {
            throw new BadMethodCallException('Missing versionid parameter');
        }
        if (!isset($data['authorid'])) {
            throw new BadMethodCallException('Missing authorid parameter');
        }
        if (!isset($data['content'])) {
            throw new BadMethodCallException('Missing content parameter');
        }
        if (!isset($data['feedbackindice'])) {
            throw new BadMethodCallException('Mising feedbackindice parameter');
        }
        if (!isset($data['creationtime'])) {
            throw new BadMethodCallException('Mising creationtime parameter');
        }

        parent::hydrate($data);
    }

    public function get_html() {
        global $DB;
        $author = $DB->get_record('user', array('id' => $this->authorid));
        return  '<aside class="sqds-bottom-right">'.
                    '<strong>'.get_string('feedbacksubmitted', 'mod_sequentialdocuments').'</strong>'.
                    userdate($this->creationtime).
                '</aside>'.
                '<strong>'.get_string('feedbackfrom', 'mod_sequentialdocuments').'</strong>'.
                $author->lastname.' '.$author->firstname.'<br /><br />'.
                '<p>'.$this->content.'</p>';
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

    public function get_feedbackindice() {
        return $this->feedbackindice;
    }

    public function get_creationtime() {
        return $this->creationtime;
    }

    public function is_locked() {
        return $this->locked;
    }

    public function set_versionid($id) {
        $this->versionid = $this->check_numeric_id($id);
    }

    public function set_authorid($id) {
        $this->authorid = $this->check_numeric_id($id);
    }

    public function set_feedbackindice($i) {
        $this->feedbackindice = $this->check_numeric_id($i);
    }

    public function set_creationtime($timestamp) {
        if(!$this->is_valid_timestamp($timestamp)) {
            throw new InvalidArgumentException('Received invalid timestamp parameter: "'.$timestamp.'"');
        }
        $this->creationtime = $timestamp;
    }

    public function set_content($content) {
        if (!is_string($content)) {
            throw new InvalidArgumentException();
        }
        $this->content = $content;
    }

    public function set_locked($bool) {
        if ($bool) {
            $this->locked = 1;
        } else {
            $this->locked = 0;
        }
    }
}


