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

global $CFG;
require_once($CFG->libdir.'/formslib.php');

class add_feedback_form extends moodleform {

    public function definition() {

        $form = $this->_form;

        $form->addElement('header', 'addfeedback', 'Post a feedback');

        $form->addElement('hidden', 'instanceid');
        $form->setType('instanceid', PARAM_INT);

        $form->addElement('hidden', 'version');
        $form->setType('version', PARAM_INT);

        global $USER;
        $form->addElement('hidden', 'authorid', $USER->id);
        $form->setType('authorid', PARAM_INT);

        $form->addElement('textarea', 'content', 'Feedback content', 'rows="20" cols="65"');
        $form->setType('content', PARAM_TEXT);
        $form->addRule('content', 'This field is required', 'required', null, 'server');
        $form->setDefault('content', $this->_customdata['content']);

        $form->addElement(
                    'filemanager',
                    'attachments',
                    'File(s)',
                    null,
                    array(
                        'subdirs' => 0,
                        'maxbytes' => 0,
                        'maxfiles' => 50,
                        'accepted_types' => array('document'),
                    )
        );

        //$this->set_data(array());
        $this->add_action_buttons();
    }

    function validation($data, $files) {
        return array();
    }
}


