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

class access_rights_config_form extends moodleform {

    public function definition() {

        $form = $this->_form;

        $form->addElement('hidden', 'accessid');
        $form->setType('accessid', PARAM_INT);



        $form->addElement('header', 'documentsaccess', 'Configure students access rights on documents');

        $form->addElement('advcheckbox', 'readdocument', 'Students can read documents', '', null, array(0, 1));
        $form->setType('readdocument', PARAM_BOOL);
        $form->setDefault('readdocument', $this->_customdata['readdocument']);

        $form->addElement('advcheckbox', 'createdocument', 'Students can create documents', '', null, array(0, 1));
        $form->setType('createdocument', PARAM_BOOL);
        $form->setDefault('createdocument', $this->_customdata['createdocument']);

        $form->addElement('advcheckbox', 'editdocument', 'Students can edit documents', '', null, array(0, 1));
        $form->setType('editdocument', PARAM_BOOL);
        $form->setDefault('editdocument', $this->_customdata['editdocument']);

        $form->addElement('advcheckbox', 'deletedocument', 'Students can delete documents', '', null, array(0, 1));
        $form->setType('deletedocument', PARAM_BOOL);
        $form->setDefault('deletedocument', $this->_customdata['deletedocument']);



        $form->addElement('header', 'documentsaccess', 'Configure students access rights on versions');

        $form->addElement('advcheckbox', 'readversion', 'Students can read versions', '', null, array(0, 1));
        $form->setType('readversion', PARAM_BOOL);
        $form->setDefault('readversion', $this->_customdata['readversion']);

        $form->addElement('advcheckbox', 'createversion', 'Students can create versions', '', null, array(0, 1));
        $form->setType('createversion', PARAM_BOOL);
        $form->setDefault('createversion', $this->_customdata['createversion']);

        $form->addElement('advcheckbox', 'editversion', 'Students can edit versions', '', null, array(0, 1));
        $form->setType('editversion', PARAM_BOOL);
        $form->setDefault('editversion', $this->_customdata['editversion']);

        $form->addElement('advcheckbox', 'deleteversion', 'Students can delete versions', '', null, array(0, 1));
        $form->setType('deleteversion', PARAM_BOOL);
        $form->setDefault('deleteversion', $this->_customdata['deleteversion']);



        $form->addElement('header', 'documentsaccess', 'Configure students access rights on feedbacks');

        $form->addElement('advcheckbox', 'readfeedback', 'Students can read feedbacks', '', null, array(0, 1));
        $form->setType('readfeedback', PARAM_BOOL);
        $form->setDefault('readfeedback', $this->_customdata['readfeedback']);

        $form->addElement('advcheckbox', 'createfeedback', 'Students can create feedbacks', '', null, array(0, 1));
        $form->setType('createfeedback', PARAM_BOOL);
        $form->setDefault('createfeedback', $this->_customdata['createfeedback']);

        $form->addElement('advcheckbox', 'editfeedback', 'Students can edit feedbacks', '', null, array(0, 1));
        $form->setType('editfeedback', PARAM_BOOL);
        $form->setDefault('editfeedback', $this->_customdata['editfeedback']);

        $form->addElement('advcheckbox', 'deletefeedback', 'Students can delete feedbacks', '', null, array(0, 1));
        $form->setType('deletefeedback', PARAM_BOOL);
        $form->setDefault('deletefeedback', $this->_customdata['deletefeedback']);

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        return array();
    }
}





