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
 * Prints a particular instance of sequentialdocuments
 *
 * @package    mod_sequentialdocuments
 * @author     Benjamin Costa
 * @copyright  (c) 2014 CNAM, Paris, France
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

global $COURSE, $DB, $PAGE, $USER;

// Course module id, or...
$id = optional_param('id', 0, PARAM_INT);
// ...sequentialdocuments instance id - it should be named as the first character of the module.
$s  = optional_param('t', 0, PARAM_INT);

$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($id) {
    $cm = get_coursemodule_from_id('sequentialdocuments', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    $sequentialdocuments = $DB->get_record(
            'sequentialdocuments',
            array('id' => $cm->instance),
            '*',
            MUST_EXIST
    );
}
elseif ($s) {
    $sequentialdocuments = $DB->get_record('sequentialdocuments', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $sequentialdocuments->course), '*', MUST_EXIST);

    $cm = get_coursemodule_from_instance(
            'sequentialdocuments',
            $sequentialdocuments->id,
            $course->id,
            false,
            MUST_EXIST
    );
}
else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/page:view', $context);

// Trigger module viewed event.
$event = \mod_page\event\course_module_viewed::create(array(
   'objectid' => $sequentialdocuments->id,
   'context' => $context
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('sequentialdocuments', $sequentialdocuments);
$event->trigger();

// Update 'viewed' state if required by completion system.
require_once($CFG->libdir . '/completionlib.php');
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

/// Print the page header.
$PAGE->set_url('/mod/sequentialdocuments/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($sequentialdocuments->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$PAGE->set_cacheable(true);

if ($inpopup and $sequentialdocuments->display == RESOURCELIB_DISPLAY_POPUP) {
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title($course->shortname.': '.$sequentialdocuments->name);
    $PAGE->set_heading($course->fullname);
}
else {
    $PAGE->set_title($course->shortname.': '.$sequentialdocuments->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($sequentialdocuments);
}

// Output starts here.
echo $OUTPUT->header();

if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($sequentialdocuments->name), 2);
}
// Conditions to show the intro can change to look for own settings or whatever.
if ($sequentialdocuments->intro) {
    echo $OUTPUT->box(
            format_module_intro(
                'sequentialdocuments', $sequentialdocuments, $cm->id
            ),
            'generalbox mod_introbox', 'sequentialdocumentsintro'
    );
}

echo $OUTPUT->footer();


