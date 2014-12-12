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
 * Library of interface functions and constants for module sequentialdocuments
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the sequentialdocuments specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_sequentialdocuments
 * @author     Benjamin Costa
 * @copyright  (c) 2014 CNAM, Paris, France
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function sequentialdocuments_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:            return true;
        case FEATURE_GROUPINGS:         return true;
        case FEATURE_GROUPMEMBERSONLY:  return true;
        case FEATURE_MOD_INTRO:         return true;
        case FEATURE_SHOW_DESCRIPTION:  return true;

        default:                        return null;
    }
}

/**
 * Saves a new instance of the sequentialdocuments into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $sequentialdocuments An object from the form in mod_form.php
 * @param mod_sequentialdocuments_mod_form $mform
 * @return int The id of the newly inserted sequentialdocuments record
 */
function sequentialdocuments_add_instance(stdClass $sequentialdocuments, mod_sequentialdocuments_mod_form $mform = null) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/group/lib.php');

    $sequentialdocuments->timecreated = time();

    $data = $mform->get_data();
    $uniqid = uniqid();
    $len = strlen($uniqid);
    $uniqid = $data->name.substr($uniqid, $len -5, $len -1);

    $grouping = new stdClass();
    $grouping->courseid = $data->courseid;
    $grouping->name = 'SQ_'.$uniqid;
    $groupingid = groups_create_grouping($grouping);

    $group = new stdClass();
    $group->courseid = $data->courseid;
    $group->name = 'sq_'.$uniqid;
    $groupid = groups_create_group($group);

    if ($groupingid !== false) {
        $sequentialdocuments->groupingid = (int)$groupingid;
    }
    if ($groupid !== false) {
        $sequentialdocuments->groupid = (int)$groupid;
    }

    foreach ($data->teachers as $teacher) {
        groups_add_member($groupid, $teacher);
    }
    foreach ($data->students as $student) {
        groups_add_member($groupid, $student);
    }
    groups_assign_grouping($groupingid, $groupid);

    /*$data->groupingid = $groupingid;
    $data->groupmode = 2;*/

    $access = new stdClass();
    $access->instanceid = $DB->insert_record('sequentialdocuments', $sequentialdocuments);

    $DB->insert_record('sequentialdocuments_access', $access);

    return $access->instanceid;
}

/**
 * Updates an instance of the sequentialdocuments in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $sequentialdocuments An object from the form in mod_form.php
 * @param mod_sequentialdocuments_mod_form $mform
 * @return boolean Success/Fail
 */
function sequentialdocuments_update_instance(stdClass $sequentialdocuments, mod_sequentialdocuments_mod_form $mform = null) {
    global $DB;

    $sequentialdocuments->timemodified = time();
    $sequentialdocuments->id = $sequentialdocuments->instance;

    return $DB->update_record('sequentialdocuments', $sequentialdocuments);
}

/**
 * Erase all informations concerning a file in the database and physicaly delete this file.
 *
 * @param int $id id of the file to delete.
 * @return void
 */
function sequentialdocuments_delete_file($id) {
    global $DB;

    $DB->delete_records_select('sequentialdocuments_comment', 'file='.$id);
    $DB->delete_records_select('sequentialdocuments_feedback', 'file='.$id);

    //TODO: physically delete the file

    $DB->delete_records_select('sequentialdocuments_file', 'id='.$id);
}

/**
 * Erase all informations concerning a version in the database.
 *
 * @param int $id id of the version to delete.
 * @return void
 */
function sequentialdocuments_delete_version($id) {
    global $DB;

    $files = $DB->get_records_select('sequentialdocuments_file', 'version='.$id);
    foreach ($files as $file) {
        sequentialdocuments_delete_file($file->id);
    }
    $DB->delete_records_select('sequentialdocuments_version', 'id='.$id);
}

/**
 * Erase all informations concerning an archive in the database.
 *
 * @param int $id id of the archive to delete.
 * @return void
 */
function sequentialdocuments_delete_archive($id) {
    global $DB;

    $versions = $DB->get_records_select('sequentialdocuments_version', 'archive='.$id);
    foreach ($versions as $version) {
        sequentialdocuments_delete_version($version->id);
    }
    $DB->delete_records_select('sequentialdocuments_archive', 'id='.$id);
}

/**
 * Removes an instance of the sequentialdocuments from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function sequentialdocuments_delete_instance($id) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/group/lib.php');

    if (! $sequentialdocuments = $DB->get_record('sequentialdocuments', array('id' => $id))) {
        return false;
    }

    groups_delete_group($sequentialdocuments->groupid);
    groups_delete_grouping($sequentialdocuments->groupingid);

    $documents = $DB->get_records_select('sequentialdocuments_document', 'instanceid='.$id);
    foreach ($documents as $document) {
        $versions = $DB->get_records_select('sequentialdocuments_version', 'documentid='.$document->id);
        foreach ($versions as $version) {
            $DB->delete_records('sequentialdocuments_feedback', array('versionid' => $version->id));
        }
        $DB->delete_records('sequentialdocuments_version', array('documentid' => $document->id));
    }
    $DB->delete_records('sequentialdocuments_document', array('instanceid' => $id));
    $DB->delete_records('sequentialdocuments_interact', array('instanceid' => $id));

    $DB->delete_records('sequentialdocuments_access', array('instanceid' => $sequentialdocuments->id));
    $DB->delete_records('sequentialdocuments', array('id' => $sequentialdocuments->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function sequentialdocuments_user_outline($course, $user, $mod, $sequentialdocuments) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $sequentialdocuments the module instance record
 * @return void, is supposed to echp directly
 */
function sequentialdocuments_user_complete($course, $user, $mod, $sequentialdocuments) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in sequentialdocuments activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function sequentialdocuments_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link sequentialdocuments_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function sequentialdocuments_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see sequentialdocuments_get_recent_mod_activity()}

 * @return void
 */
function sequentialdocuments_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function sequentialdocuments_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function sequentialdocuments_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of sequentialdocuments?
 *
 * This function returns if a scale is being used by one sequentialdocuments
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $sequentialdocumentsid ID of an instance of this module
 * @return bool true if the scale is used by the given sequentialdocuments instance
 */
function sequentialdocuments_scale_used($sequentialdocumentsid, $scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('sequentialdocuments', array('id' => $sequentialdocumentsid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of sequentialdocuments.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any sequentialdocuments instance
 */
function sequentialdocuments_scale_used_anywhere($scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('sequentialdocuments', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the give sequentialdocuments instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $sequentialdocuments instance object with extra cmidnumber and modname property
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return void
 */
function sequentialdocuments_grade_item_update(stdClass $sequentialdocuments, $grades=null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($sequentialdocuments->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $sequentialdocuments->grade;
    $item['grademin']  = 0;

    grade_update('mod/sequentialdocuments', $sequentialdocuments->course, 'mod', 'sequentialdocuments', $sequentialdocuments->id, 0, null, $item);
}

/**
 * Update sequentialdocuments grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $sequentialdocuments instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function sequentialdocuments_update_grades(stdClass $sequentialdocuments, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/sequentialdocuments', $sequentialdocuments->course, 'mod', 'sequentialdocuments', $sequentialdocuments->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function sequentialdocuments_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for sequentialdocuments file areas
 *
 * @package mod_sequentialdocuments
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function sequentialdocuments_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the sequentialdocuments file areas
 *
 * @package mod_sequentialdocuments
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the sequentialdocuments's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function sequentialdocuments_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'version' && $filearea !== 'feedback') {
        return false;
    }

    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    require_login($course, true, $cm);

    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    /*if (!has_capability('mod/MYPLUGIN:view', $context)) {
        return false;
    }*/

    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.

    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_sequentialdocuments', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    // From Moodle 2.3, use send_stored_file instead.
    //send_file($file, 86400, 0, $forcedownload, $options);
    send_stored_file($file, 86400, 0, true);
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding sequentialdocuments nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the sequentialdocuments module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function sequentialdocuments_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
    //global $PAGE;
    //$nodeFoo = $PAGE->navigation->add('Foo');
    //$nodeBar = $nodeFoo->add('Bar');

    global $CFG, $PAGE, $DB;


}

/**
 * Extends the settings navigation with the sequentialdocuments settings
 *
 * This function is called when the context for the page is a sequentialdocuments module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $sequentialdocumentsnode {@link navigation_node}
 */
function sequentialdocuments_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $sequentialdocumentsnode=null) {
}


