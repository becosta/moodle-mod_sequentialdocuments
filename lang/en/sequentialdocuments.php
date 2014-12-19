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
 * English strings for sequentialdocuments
 *
 * @package    mod_sequentialdocuments
 * @author     Benjamin Costa
 * @copyright  (c) 2014 CNAM, Paris, France
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Sequential Documents';
$string['modulenameplural'] = 'Sequential Documents';
$string['pluginname'] = 'Sequential Documents';

$string['sequentialdocuments'] = 'sequentialdocuments';
$string['pluginadministration'] = 'Sequential Documents administration';

$string['sequentialdocumentsname'] = 'Sequential Documents';
$string['sequentialdocumentssname_help'] = 'This is the content of the help tooltip associated with the sequentialdocumentsname field. Markdown syntax is supported.';

$string['modulename_help'] = '';

// Instance tabs.
$string['tabhistory'] = 'History';
$string['tabindex'] = 'Documents';
$string['tabnewdocument'] = 'Create new document';
$string['tabaccessrights'] = 'Edit access rights';

// Document objects html.
$string['documenttitle'] = 'Title:';
$string['documentdescription'] = 'Description:';
$string['documentcreated'] = 'Created:';
$string['documentmodified'] = 'Modified:';
$string['documentcontent'] = 'Content:';
$string['documentaddversionlink'] = 'Add new version';
$string['documenteditlink'] = 'Edit';
$string['documentdeletelink'] = 'Delete';
$string['documentlocklink'] = 'Lock this document';
$string['documentunlocklink'] = 'Unlock this document';

// Version objects html.
$string['versionlink'] = 'Version: ';
$string['versionsubmitted'] = 'Submitted: ';
$string['versionduedate'] = 'Due date:';
$string['versioneditlink'] = $string['documenteditlink'];
$string['versionaddfilelink'] = 'Attach file';
$string['versiondeletelink'] = $string['documentdeletelink'];
$string['versionpostfeedbacklink'] = 'Post feedback';
$string['versionlocklink'] = 'Lock this version';
$string['versionunlocklink'] = 'Unlock this version';

// Feedback
$string['feedbackattachments'] = 'Attachments: ';
$string['feedbackeditlink'] = $string['documenteditlink'];
$string['feedbackdeletelink'] = $string['documentdeletelink'];
$string['feedbackcreateversionfromthis'] = 'Create new version from this feedback';

// Feedback objects html.
$string['feedbacksubmitted'] = $string['versionsubmitted'];
$string['feedbackfrom'] = 'Feedback from: ';

// Interaction objects html.
$string['interacttitle'] = $string['documenttitle'];
$string['interactdescription'] = $string['documentdescription'];
$string['interactfrom'] = 'From: ';
$string['interacton'] = 'On: ';
$string['interactnewdocument'] = 'New document ';
$string['interactnewversion'] = 'New version ';
$string['interactnewfeedback'] = 'New feedback ';

// Versions due dates reminders.
$string['versionduesubject'] = 'Version due date reminder';
$string['automaticmessage'] = 'this is an automatic message';
$string['versionduecorp'] = 'A document version is due ';
$string['versionduelatesubject'] = 'Version due date passed reminder';
$string['versionduelink'] = 'See this version';
$string['versionduedueday'] = $string['versionduecorp'].'today.<br />('.$string['automaticmessage'].')<br />';
$string['versiondueoneday'] = $string['versionduecorp'].'tomorrow.<br />('.$string['automaticmessage'].')<br />';
$string['versiondueoneweek'] = $string['versionduecorp'].'in a week.<br />('.$string['automaticmessage'].')<br />';
$string['versionduetwoweeks'] = $string['versionduecorp'].'in two weeks.<br />('.$string['automaticmessage'].')<br />';
$string['versiondueonemonth'] = $string['versionduecorp'].'in a month.<br />('.$string['automaticmessage'].')<br />';
$string['versionduelate'] = 'A document version is late.<br />('.$string['automaticmessage'].')';

// Forms
$string['requiredfield'] = 'This field is required';
$string['files'] = 'File(s)';

// mod_form.php
$string['mfteachers'] = 'Teachers';
$string['mfstudents'] = 'Students';

// add_document_form.php
$string['adfadddocument'] = 'Add a document';
$string['adftitle'] = 'Document title';
$string['adfdescription'] = 'Document description';
$string['adfrequired'] = $string['requiredfield'];

// add_version_form.php
$string['avfaddversion'] = 'Add a document version';
$string['avfduetime'] = 'Due date: ';
$string['avfsendreminder'] = 'Send a reminder ';
$string['avfdueday'] = $string['avfsendreminder'].'on due day';
$string['avfoneday'] = $string['avfsendreminder'].'on due eve';
$string['avfoneweek'] = $string['avfsendreminder'].'one week before the due day';
$string['avftwoweeks'] = $string['avfsendreminder'].'two weeks before the due day';
$string['avfonemonth'] = $string['avfsendreminder'].'one month before the due day';
$string['avfpostneeded'] = $string['avfsendreminder'].'that this version is late for:';
$string['avfselectnever'] = 'Never';
$string['avfselectoneweek'] = '1 week';
$string['avfselecttwoweeks'] = '2 weeks';
$string['avfselectthreeweeks'] = '3 weeks';
$string['avfselectonemonth'] = '1 month';
$string['avfselecttwomonth'] = '2 months';
$string['avfselectthreemonth'] = '3 months';
$string['avfselectsixmonth'] = '6 months';
$string['avffiles'] = $string['files'];
$string['avfrequired'] = $string['requiredfield'];

// add_feedback_form.php
$string['affaddfeedback'] = 'Post a feedback';
$string['affcontent'] = 'Feedback content';
$string['afffiles'] = $string['files'];
$string['affrequired'] = $string['requiredfield'];


// access_rights_config_form.php
$string['arfdocumentaccess'] = 'Configure students access rights on documents';
$string['arfreaddocument'] = 'Students can read documents';
$string['arfcreatedocument'] = 'Students can create documents';
$string['arfeditdocument'] = 'Students can edit documents';
$string['arfdeletedocument'] = 'Students can delete documents';
$string['arfversionaccess'] = 'Configure students access rights on versions';
$string['arfreadversion'] = 'Students can read versions';
$string['arfcreateversion'] = 'Students can create versions';
$string['arfeditversion'] = 'Students can edit versions';
$string['arfdeleteversion'] = 'Students can delete versions';
$string['arffeedbackaccess'] = 'Configure students access rights on feedbacks';
$string['arfreadfeedback'] = 'Students can read feedbacks';
$string['arfcreatefeedback'] = 'Students can create feedbacks';
$string['arfeditfeedback'] = 'Students can edit feedbacks';
$string['arfdeletefeedback'] = 'Students can delete feedbacks';

// Messages.
$string['messageprovider:duedatereminder'] = 'Reminders of versions due dates';
$string['messageprovider:newsubmission'] = 'Notifies you that a student posted a new submission';

// Cron tasks.
$string['sendreminders'] = 'Send Sequential Documents reminders';

// Errors.
$string['error'] = 'Error';
$string['unknownerror'] = 'Unknown error';
$string['pagenotfound'] = 'Page not found';
$string['missingcmorid'] = 'You must specify a course_module ID or an instance ID';
$string['missinginstanceaccess'] = 'You don\'t have access to this '.$string['modulename'].' instance';
$string['missinglockingrights'] = 'You don\'t have locking rights';
$string['missingpageaccess'] = 'You don\'t have access to this page';
$string['invaliddocumentid'] = 'Invalid document id specified';
$string['invalidversionid'] = 'Invalid version id specified';
$string['invalidfeedbackid'] = 'Invalid feedback id specified';
$string['missingdocumentaccess'] = 'You don\'t have access to this document';
$string['missingdocumentcreationrights'] = 'You don\'t have permissions to create documents';
$string['missingdocumenteditionrights'] = 'You don\'t have permissions to edit documents';
$string['missingdocumentsuppressionrights'] = 'You don\'t have suppression rights on this document';
$string['missingdocumentlockingrights'] = 'You don\'t have locking rights on this document';
$string['missingversionaccess'] = 'You don\'t have access to this document version';
$string['missingversioncreationrights'] = 'You don\'t have permissions to create version';
$string['missingversioneditionrights'] = 'You don\'t have permissions to edit version';
$string['missingversionsuppressionrights'] = 'You don\'t have suppression rights on this version';
$string['missingversionlockingrights'] = 'You don\'t have locking rights on this version';
$string['missingfeedbackaccess'] = 'You don\'t have access to this feedback';
$string['missingfeedbackcreationrights'] = 'You don\'t have permissions to post feedback';
$string['missingfeedbackeditionrights'] = 'You don\'t have permissions to edit feedback';
$string['missingfeedbacksuppressionrights'] = 'You don\'t have suppression rights on this feedback';
$string['missingfeedbacklockingrights'] = 'You don\'t have locking rights on this feedback';

// unauthorized_access_exception.php
$string['uainstance'] = $string['missinginstanceaccess'];
$string['ua1000'] = $string['missingdocumentaccess'];
$string['ua1001'] = $string['missingdocumentsuppressionrights'];
$string['ua1002'] = $string['missingversionsuppressionrights'];
$string['ua1003'] = $string['missingfeedbacksuppressionrights'];
$string['ua1004'] = $string['missingdocumentlockingrights'];
$string['ua1005'] = $string['missingversionlockingrights'];
$string['ua1006'] = $string['missingfeedbacklockingrights'];

// inexistent_entity_exception.php
$string['uedocument'] = 'This document doesn\'t exist';
$string['ueversion'] = 'This version doesn\'t exist';
$string['uefeedback'] = 'This feedback doesn\'t exist';


