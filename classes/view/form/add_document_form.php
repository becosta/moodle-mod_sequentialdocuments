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

class add_document_form extends moodleform {

    public function definition() {

        $form = $this->_form;

        $form->addElement('header', 'adddocument', get_string('adfadddocument', 'mod_sequentialdocuments'));

        $form->addElement('hidden', 'instanceid');
        $form->setType('instanceid', PARAM_INT);

        $form->addElement('text', 'title', get_string('adftitle', 'mod_sequentialdocuments'));
        $form->setType('title', PARAM_TEXT);
        $form->addRule(
                'title', get_string('adfrequired', 'mod_sequentialdocuments'), 'required', null, 'server'
        );
        $form->setDefault('title', $this->_customdata['title']);

        $form->addElement(
                'textarea', 'description', get_string('adfdescription', 'mod_sequentialdocuments'), 'rows="20" cols="65"'
        );
        $form->setType('description', PARAM_TEXT);
        $form->addRule('description', 'This field is required', 'required', null, 'server');
        $form->setDefault('description', $this->_customdata['description']);

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        return array();
    }
}


