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
 * Internal library of functions for module sequentialdocuments
 *
 * All the sequentialdocuments specific functions, needed to implement the module
 * logic, is contained here. Never include this file from your lib.php!
 *
 * @package    mod_sequentialdocuments
 * @author     Benjamin Costa
 * @copyright  (c) 2014 CNAM, Paris, France
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function sequentialdocuments_get_course_context($instanceid) {
    global $CFG, $COURSE, $DB;

    if (! $sequentialdocuments = $DB->get_record('sequentialdocuments', array('id' => $instanceid))) {
        return false;
    }

    $context = context_course::instance($sequentialdocuments->course);
    return $context;
}

function sequentialdocuments_is_instance_member($instanceid, $userid) {
    global $CFG, $COURSE, $DB;

    if (!$sequentialdocuments = $DB->get_record('sequentialdocuments', array('id' => $instanceid))) {
        return false;
    }

    $context = context_course::instance($sequentialdocuments->course);
    if (has_capability('mod/sequentialdocuments:manager', $context)) {
        return true;
    }

    require_once($CFG->dirroot.'/group/lib.php');
    $roles = groups_get_members_by_role($sequentialdocuments->groupid, $sequentialdocuments->course);

    if ($roles) {
        foreach ($roles as $role) {
            foreach ($role->users as $id => $user) {
                if ($id == $userid) {
                    return true;
                }
            }
        }
    }
    return false;
}

function sequentialdocuments_has_global_read_rights($instanceid, $userid) {
    $context = sequentialdocuments_get_course_context($instanceid);
    return  (
                sequentialdocuments_is_instance_member($instanceid, $userid) &&
                has_capability('mod/sequentialdocuments:teacher', $context)
            )
            || has_capability('mod/sequentialdocuments:manager', $context)
    ;
}

function sequentialdocuments_has_document_read_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_read_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_read_document($instanceid)
            )
    ;
}

function sequentialdocuments_has_version_read_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_read_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_read_version($instanceid)
            )
    ;
}

function sequentialdocuments_has_feedback_read_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_read_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_read_feedback($instanceid)
            )
    ;
}

function sequentialdocuments_has_global_creation_rights($instanceid, $userid) {
    $context = sequentialdocuments_get_course_context($instanceid);
    return  (
                sequentialdocuments_is_instance_member($instanceid, $userid) &&
                has_capability('mod/sequentialdocuments:teacher', $context)
            )
            || has_capability('mod/sequentialdocuments:manager', $context)
    ;
}

function sequentialdocuments_has_document_creation_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_creation_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_create_document($instanceid)
            )
    ;
}

function sequentialdocuments_has_version_creation_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_creation_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_create_version($instanceid)
            )
    ;
}

function sequentialdocuments_has_feedback_creation_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_creation_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_create_feedback($instanceid)
            )
    ;
}

function sequentialdocuments_has_global_edit_rights($instanceid, $userid) {
    $context = sequentialdocuments_get_course_context($instanceid);
    return  (
                sequentialdocuments_is_instance_member($instanceid, $userid) &&
                has_capability('mod/sequentialdocuments:teacher', $context)
            )
            || has_capability('mod/sequentialdocuments:manager', $context)
    ;
}

function sequentialdocuments_has_document_edit_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_edit_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_edit_document($instanceid)
            )
    ;
}

function sequentialdocuments_has_version_edit_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_edit_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_edit_version($instanceid)
            )
    ;
}

function sequentialdocuments_has_feedback_edit_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_edit_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_edit_feedback($instanceid)
            )
    ;
}

function sequentialdocuments_has_global_suppression_rights($instanceid, $userid) {
    $context = sequentialdocuments_get_course_context($instanceid);
    return  (
                sequentialdocuments_is_instance_member($instanceid, $userid) &&
                has_capability('mod/sequentialdocuments:teacher', $context)
            )
            || has_capability('mod/sequentialdocuments:manager', $context)
    ;
}

function sequentialdocuments_has_document_suppression_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_suppression_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_delete_document($instanceid)
            )
    ;
}

function sequentialdocuments_has_version_suppression_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_suppression_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_delete_version($instanceid)
            )
    ;
}

function sequentialdocuments_has_feedback_suppression_rights($instanceid, $userid) {
    return sequentialdocuments_has_global_suppression_rights($instanceid, $userid) ||
            (
                has_capability(
                    'mod/sequentialdocuments:student',
                    sequentialdocuments_get_course_context($instanceid)
                ) && students_can_delete_feedback($instanceid)
            )
    ;
}

function sequentialdocuments_has_lock_document_rights($instanceid, $userid) {
    $context = sequentialdocuments_get_course_context($instanceid);
    return  (
                sequentialdocuments_is_instance_member($instanceid, $userid) &&
                has_capability('mod/sequentialdocuments:teacher', $context)
            )
            || has_capability('mod/sequentialdocuments:manager', $context)
    ;
}

function sequentialdocuments_has_lock_version_rights($instanceid, $userid) {
	return sequentialdocuments_has_lock_document_rights($instanceid, $userid);
}

function sequentialdocuments_has_lock_feedback_rights($instanceid, $userid) {
	return sequentialdocuments_has_lock_document_rights($instanceid, $userid);
}

function sequentialdocuments_students_can($action, $instanceid) {
    global $DB;
    $access = $DB->get_records('sequentialdocuments_access', array('instanceid' => $instanceid));
    if ($access !== false) {
        if (isset($access[1]->$action) && $access[1]->$action == 1) {
			return true;
        }
    }
    return false;
}

function students_can_read_document($instanceid) {
    return sequentialdocuments_students_can('readdocument', $instanceid);
}

function students_can_read_version($instanceid) {
    return sequentialdocuments_students_can('readversion', $instanceid);
}

function students_can_read_feedback($instanceid) {
    return sequentialdocuments_students_can('readfeedback', $instanceid);
}

function students_can_create_document($instanceid) {
    return sequentialdocuments_students_can('createdocument', $instanceid);
}

function students_can_create_version($instanceid) {
    return sequentialdocuments_students_can('createversion', $instanceid);
}

function students_can_create_feedback($instanceid) {
    return sequentialdocuments_students_can('createfeedback', $instanceid);
}

function students_can_edit_document($instanceid) {
    return sequentialdocuments_students_can('editdocuments', $instanceid);
}

function students_can_edit_version($instanceid) {
    return sequentialdocuments_students_can('editversion', $instanceid);
}

function students_can_edit_feedback($instanceid) {
    return sequentialdocuments_students_can('editfeedback', $instanceid);
}

function students_can_delete_document($instanceid) {
    return sequentialdocuments_students_can('deletedocument', $instanceid);
}

function students_can_delete_version($instanceid) {
    return sequentialdocuments_students_can('deleteversion', $instanceid);
}

function students_can_delete_feedback($instanceid) {
    return sequentialdocuments_students_can('deletefeedback', $instanceid);
}

function get_plugin_base_url() {
    global $CFG;
    return $CFG->wwwroot.'/mod/sequentialdocuments';
}

function get_view_index_url($instanceid) {
    return get_plugin_base_url().'/view.php?s='.$instanceid;
}

function get_view_history_url($instanceid) {
    return get_plugin_base_url().'/view.php?s='.$instanceid.'&action=history';
}

function get_add_document_url($instanceid) {
    return get_plugin_base_url().'/view.php?s='.$instanceid.'&action=add_document';
}

function get_add_version_url($documentid, $instanceid) {
    return get_plugin_base_url().'/view.php?s='.$instanceid.'&action=add_version&documentid='.$documentid;
}

function get_add_feedback_url($versionid, $instanceid) {
    return get_plugin_base_url().'/view.php?s='.$instanceid.'&action=add_feedback&versionid='.$versionid;
}

function get_view_entity_url($entityclassname, $entityid, $instanceid) {
    return get_plugin_base_url().'/view.php?s='.$instanceid.
            '&action=view_'.$entityclassname.'&'.$entityclassname.'id='.$entityid;
}

function get_view_interaction_url($interactionid, $instanceid) {
    return get_view_entity_url('interaction', $interactionid, $instanceid);
}

function get_view_document_url($documentid, $instanceid) {
    return get_view_entity_url('document', $documentid, $instanceid);
}

function get_view_version_url($versionid, $instanceid) {
    return get_view_entity_url('version', $versionid, $instanceid);
}

function get_view_feedback_url($feedbackid, $instanceid) {
    return get_view_entity_url('feedback', $feedbackid, $instanceid);
}

function get_update_entity_url($entityclassname, $entityid, $instanceid) {
    return get_plugin_base_url().'/view.php?s='.$instanceid.
            '&action=update_'.$entityclassname.'&'.$entityclassname.'id='.$entityid;
}

function get_update_interaction_url($interactionid, $instanceid) {
    return get_update_entity_url('interaction', $interactionid, $instanceid);
}

function get_update_document_url($documentid, $instanceid) {
    return get_update_entity_url('document', $documentid, $instanceid);
}

function get_update_version_url($versionid, $instanceid) {
    return get_update_entity_url('version', $versionid, $instanceid);
}

function get_update_feedback_url($feedbackid, $instanceid) {
    return get_update_entity_url('feedback', $feedbackid, $instanceid);
}

function get_delete_entity_url($entityclassname, $entityid, $instanceid) {
    return get_plugin_base_url().'/view.php?s='.$instanceid.
            '&action=delete_'.$entityclassname.'&'.$entityclassname.'id='.$entityid;
}

function get_delete_interaction_url($interactionid, $instanceid) {
    return get_delete_entity_url('interaction', $interactionid, $instanceid);
}

function get_delete_document_url($documentid, $instanceid) {
    return get_delete_entity_url('document', $documentid, $instanceid);
}

function get_delete_version_url($versionid, $instanceid) {
    return get_delete_entity_url('version', $versionid, $instanceid);
}

function get_delete_feedback_url($feedbackid, $instanceid) {
    return get_delete_entity_url('feedback', $feedbackid, $instanceid);
}

function get_lock_document_url($documentid, $instanceid) {
    return get_plugin_base_url().'/view.php?action=lock_document&s='.$instanceid.'&documentid='.$documentid;
}

function get_unlock_document_url($documentid, $instanceid) {
    return get_plugin_base_url().'/view.php?action=unlock_document&s='.$instanceid.'&documentid='.$documentid;
}


