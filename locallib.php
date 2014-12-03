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


