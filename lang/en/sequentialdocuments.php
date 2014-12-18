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
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_sequentialdocuments
 * @author     Benjamin Costa
 * @copyright  (c) 2014 CNAM, Paris, France
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Sequential Documents';
$string['modulenameplural'] = 'Sequential Documents';
$string['pluginname'] = 'sequentialdocuments';

$string['sequentialdocuments'] = 'sequentialdocuments';
$string['pluginadministration'] = 'Sequential Documents administration';

$string['sequentialdocumentsname'] = 'Sequential Documents';
$string['sequentialdocumentssname_help'] = 'This is the content of the help tooltip associated with the sequentialdocumentsname field. Markdown syntax is supported.';

$string['modulename_help'] = '';

// Versions due dates reminders.
$string['versionduelink'] = 'See this version';
$string['versionduedueday'] = 'A document version is due today.<br />(this is an automatic message)<br />';
$string['versiondueoneday'] = 'A document version is due tomorrow.<br />(this is an automatic message)<br />';
$string['versiondueoneweek'] = 'A document version is due in a week.<br />(this is an automatic message)<br />';
$string['versionduetwoweeks'] = 'A document version is due in two weeks.<br />(this is an automatic message)<br />';
$string['versiondueonemonth'] = 'A document version is due in a month.<br />(this is an automatic message)<br />';
$string['versionduelate'] = 'A document version is late.<br />(this is an automatic message)';

// Messages.
$string['messageprovider:duedatereminder'] = 'Reminders of versions due dates';
$string['messageprovider:newsubmission'] = 'Notifies you that a student posted a new submission';

// Cron tasks.
$string['sendreminders'] = 'Send Sequential Documents reminders';
