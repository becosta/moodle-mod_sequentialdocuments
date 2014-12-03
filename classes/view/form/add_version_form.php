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

        $cm = $this->_customdata['cm'];
        /*protected $authorid = -1;
        protected $title = '';
        protected $documentindice = -1;
        protected $currentversionid = -1;
        protected $creationtime = -1;
        protected $modificationtime = -1;*/

        $form = $this->_form;

        $form->addElement('header', 'addversion', 'Add a document version');

        /*$form->addElement('hidden', 'cmid', $cm->id);
        $form->setType('cmid', PARAM_INT);*/

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
        $form->addRule('attachments', 'This field is required', 'required', null, 'server');

        //$this->set_data(array());
        $this->add_action_buttons();
    }

    function validation($data, $files) {
        return array();
    }
}


