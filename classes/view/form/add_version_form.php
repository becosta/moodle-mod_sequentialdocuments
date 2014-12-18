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

        $form->addElement('header', 'addversion', 'Add a document version');

        $isteacher = sequentialdocuments_current_user_is_instance_teacher($this->_customdata['instanceid']);
        if ($isteacher) {
            $form->addElement('date_time_selector', 'duetime', 'Due date: ', array('optional'=>true));
            $form->setDefault('duetime', $this->_customdata['duetime']);

            $form->addElement('advcheckbox', 'dueday', 'Send a reminder on due day', '', null, array(0, 1));
            $form->setDefault('dueday', $this->_customdata['dueday']);

            $form->addElement('advcheckbox', 'oneday', 'Send a reminder on due day eve', '', null, array(0, 1));
            $form->setDefault('oneday', $this->_customdata['oneday']);

            $form->addElement('advcheckbox', 'oneweek', 'Send a reminder one week before the due day', '', null, array(0, 1));
            $form->setDefault('oneweek', $this->_customdata['oneweek']);

            $form->addElement('advcheckbox', 'twoweeks', 'Send a reminder two weeks before the due day', '', null, array(0, 1));
            $form->setDefault('twoweeks', $this->_customdata['twoweeks']);

            $form->addElement('advcheckbox', 'onemonth', 'Send a reminder one month before the due day', '', null, array(0, 1));
            $form->setDefault('onemonth', $this->_customdata['onemonth']);

            $options = array(
                '0' => 'Never',
                '1' => '1 week',
                '2' => '2 weeks',
                '3' => '3 weeks',
                '4' => '1 month',
                '8' => '2 months',
                '12' => '3 months',
                '24' => '6 months',
            );
            $select = $form->addElement('select', 'postneeded', 'Send reminder that this version is late for:', $options);
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
                    'File(s)',
                    null,
                    array(
                        'subdirs' => 0,
                        'maxbytes' => 0,
                        'maxfiles' => 50,
                        'accepted_types' => array('document'),
                    )
        );
        if (!$isteacher) {
            $form->addRule('attachments', 'This field is required', 'required', null, 'server');
        }

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        return array();
    }
}


