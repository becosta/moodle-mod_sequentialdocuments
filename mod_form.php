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
 * The main sequentialdocuments configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_sequentialdocuments
 * @author     Benjamin Costa
 * @copyright  (c) 2014 CNAM, Paris, France
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_sequentialdocuments_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {

        $mform = $this->_form;

        // Add the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Add the standard "name" field.
        $mform->addElement(
                'text',
                'name',
                get_string('sequentialdocumentsname', 'sequentialdocuments'),
                array('size' => '64')
        );
        $mform->setType('name', PARAM_TEXT);

        // Add the standard "intro" and "introformat" fields.
        $this->add_intro_editor();

        global $COURSE;
        $context = context_course::instance($COURSE->id);
        $mform->addElement('hidden', 'courseid', $COURSE->id);
        $mform->setType('courseid', PARAM_INT);

        $teachers = get_enrolled_users($context, 'mod/sequentialdocuments:teacher');
        $arr = array();
        foreach ($teachers as $teacher) {
            $arr[$teacher->id] = $teacher->lastname.' '.$teacher->firstname;
        }
        $selectteachers = $mform->addElement(
                                    'select',
                                    'teachers',
                                    get_string('mfteachers', 'mod_sequentialdocuments'),
                                    $arr
        );
        $selectteachers->setMultiple(true);

        $students = get_enrolled_users($context, 'mod/sequentialdocuments:student');
        $arr = array();
        foreach ($students as $student) {
            $arr[$student->id] = $student->lastname.' '.$student->firstname;
        }
        $selectstudents = $mform->addElement(
                                    'select',
                                    'students',
                                    get_string('mfstudents', 'mod_sequentialdocuments'),
                                    $arr
        );
        $selectstudents->setMultiple(true);

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
}


