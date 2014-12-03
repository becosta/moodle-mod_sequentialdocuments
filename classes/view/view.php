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

abstract class view {

    protected $instanceid = -1;
    protected $title = '';
    protected $intro = '';
    protected $content = '';
    protected $footer = '';
    protected $layout = '';

    public function __construct(array $data = null) {
        if ($data !== null) {
            $this->hydrate($data);
        }
    }

    protected function hydrate($data) {
        if (!isset($data['instanceid'])) {
            throw new BadMethodCallException('Missing instanceid parameter');
        }

        foreach ($data as $property => $value) {
            $setter = 'set_'.$property;
            if (is_callable(array($this, $setter))) {
                $this->$setter($value);
            }
        }
    }

    public function display() {
        $this->display_header();
        $this->display_content();
        $this->display_footer();
    }

    public function display_header() {
        global $OUTPUT;

        echo $OUTPUT->header();
        echo $OUTPUT->heading(format_string($this->title), 2);
        if ($this->intro != '') {
            echo $OUTPUT->box(
                    $this->intro,
                    'generalbox mod_introbox', 'sequentialdocumentsintro'
            );
        }
    }

    public function display_content() {

        $tabs =
            array(
                array(
                    new tabobject('history', get_view_history_url($this->instanceid), 'History'),
                    new tabobject('index', get_view_index_url($this->instanceid), 'Documents'),
                    new tabobject('add_document', get_add_document_url($this->instanceid), 'Add a document'),
                    //new tabobject('options', 'url', 'Options'),
                ),
            );

        print_tabs($tabs);
        echo $this->content;
    }

    public function display_footer() {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }

    public function get_title() {
        return $this->title;
    }

    public function get_intro() {
        return $this->intro;
    }

    public function get_content() {
        return $this->content;
    }

    public function get_footer() {
        return $this->footer;
    }

    public function get_layout() {
        return $this->layout;
    }

    protected function set_instanceid($id) {
        $this->instanceid = $id;
    }

    public function set_title($title) {
        $this->title = $title;
    }

    public function set_intro($intro) {
        $this->intro = $intro;
    }

    public function set_content($content) {
        $this->content = $content;
    }

    public function set_footer($footer) {
        $this->footer = $footer;
    }

    public function set_layout($layout) {
        $this->layout = $layout;
    }
}


