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

include_once __DIR__.'/view.php';

class form_view extends view {

    protected $form = null;

    public function __construct(array $data = null) {
        parent::__construct($data);
        $this->layout = 'form';
    }

    public function generate_content(array $data) {
        if (isset($data['content'])) {
            $this->content = $data['content'];
        } else {
            $this->content = '';
        }
    }

    public function display() {
        $this->display_header();
        $this->display_content();
        $this->display_footer();
    }

    public function display_header() {
        global $PAGE, $OUTPUT;

        echo $OUTPUT->header();
        //echo $OUTPUT->heading(format_string($this->title), 2);
        if ($this->intro != '') {
            echo $OUTPUT->box(
                    /*format_module_intro(
                        'sequentialdocuments', $sequentialdocuments, $cm->id
                    ),*/
                    $this->intro,
                    'generalbox mod_introbox', 'sequentialdocumentsintro'
            );
        }
    }

    public function display_content($displayform = true) {
        echo $this->content;
        if (is_bool($displayform) && $displayform) {
            $this->form->display();
        }
    }

    public function display_footer() {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }

    public function set_form(moodleform $form) {
        $this->form = $form;
    }
}


