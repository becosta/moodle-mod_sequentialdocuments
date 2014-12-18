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

class add_version_form extends moodleform {

    public function definition() {

        $form = $this->_form;

        $form->addElement('header', 'addversion', get_string('avfaddversion', 'mod_sequentialdocuments'));

        $isteacher = sequentialdocuments_current_user_is_instance_teacher($this->_customdata['instanceid']);
        if ($isteacher) {
            $form->addElement(
                    'date_time_selector', 'duetime',
                    get_string('avfduetime', 'mod_sequentialdocuments'),
                    array('optional'=>true)
            );
            $form->setDefault('duetime', $this->_customdata['duetime']);

            $form->addElement(
                    'advcheckbox', 'dueday',
                    get_string('avfdueday', 'mod_sequentialdocuments'),
                    '', null, array(0, 1)
            );
            $form->setDefault('dueday', $this->_customdata['dueday']);

            $form->addElement(
                    'advcheckbox', 'oneday',
                    get_string('avfoneday', 'mod_sequentialdocuments'),
                    '', null, array(0, 1)
            );
            $form->setDefault('oneday', $this->_customdata['oneday']);

            $form->addElement(
                    'advcheckbox', 'oneweek',
                    get_string('avfoneweek', 'mod_sequentialdocuments'),
                    '', null, array(0, 1)
            );
            $form->setDefault('oneweek', $this->_customdata['oneweek']);

            $form->addElement(
                    'advcheckbox', 'twoweeks',
                    get_string('avftwoweeks', 'mod_sequentialdocuments'),
                    '', null, array(0, 1)
            );
            $form->setDefault('twoweeks', $this->_customdata['twoweeks']);

            $form->addElement(
                    'advcheckbox', 'onemonth',
                    get_string('avfonemonth', 'mod_sequentialdocuments'),
                    '', null, array(0, 1)
            );
            $form->setDefault('onemonth', $this->_customdata['onemonth']);

            $options = array(
                '0' => get_string('avfselectnever', 'mod_sequentialdocuments'),
                '1' => get_string('avfselectoneweek', 'mod_sequentialdocuments'),
                '2' => get_string('avfselecttwoweeks', 'mod_sequentialdocuments'),
                '3' => get_string('avfselectthreeweeks', 'mod_sequentialdocuments'),
                '4' => get_string('avfselectonemonth', 'mod_sequentialdocuments'),
                '8' => get_string('avfselecttwomonth', 'mod_sequentialdocuments'),
                '12' => get_string('avfselectthreemonth', 'mod_sequentialdocuments'),
                '24' => get_string('avfselectsixmonth', 'mod_sequentialdocuments'),
            );
            $select = $form->addElement(
                    'select', 'postneeded',
                    get_string('avfpostneeded', 'mod_sequentialdocuments'),
                    $options
            );
            if (isset($this->_customdata['postneeded'])) {
                $select->setSelected($this->_customdata['postneeded']);
            } else {
                $select->setSelected('0');
            }
        }

        $draftid = file_get_submitted_draft_itemid('versionfiles');
        $form->addElement('hidden', 'draftid', $draftid);
        $form->setType('draftid', PARAM_INT);

        $form->addElement('hidden', 'instanceid');
        $form->setType('instanceid', PARAM_INT);

        $form->addElement(
                    'filemanager',
                    'attachments',
                    get_string('avffiles', 'mod_sequentialdocuments'),
                    null,
                    array(
                        'subdirs' => 0,
                        'maxbytes' => 0,
                        'maxfiles' => 50,
                        'accepted_types' => array('document'),
                    )
        );
        if (!$isteacher) {
            $form->addRule(
                    'attachments',
                    get_string('avfrequired', 'mod_sequentialdocuments'),
                    'required', null, 'server'
            );
        }

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        return array();
    }
}


