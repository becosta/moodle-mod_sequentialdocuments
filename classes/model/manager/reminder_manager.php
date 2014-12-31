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

include_once __DIR__.'/manager.php';
include_once __DIR__.'/../dao/reminder_dao.php';
include_once __DIR__.'/../entity/reminder.php';
include_once __DIR__.'/../../../locallib.php';

class reminder_manager extends manager {

    const ENTITY_NAME = 'reminder';

    public function __construct(array $data = null) {
        parent::__construct($data);
        $this->dao = new reminder_dao();
    }

    public function prepare_form_data($reminder) {

        if ($reminder !== false) {
            return array(
                'dueday' => $reminder->get_dueday(),
                'oneday' => $reminder->get_oneday(),
                'oneweek' => $reminder->get_oneweek(),
                'twoweeks' => $reminder->get_twoweeks(),
                'onemonth' => $reminder->get_onemonth(),
                'postneeded' => $reminder->get_postneeded(),
                'duetime' => $reminder->get_duetime(),
            );
        }
        return array(
            'dueday' => 1,
            'oneday' => 1,
            'oneweek' => 1,
            'twoweeks' => 0,
            'onemonth' => 0,
            'postneeded' => 0,
            'duetime' => 0,
        );
    }

    public function create_reminder(stdClass $data) {
        $reminder = $this->get_entity_instance_from_stdClass(static::ENTITY_NAME, $data);
        $reminder->set_id($this->dao->insert($reminder));
        return $reminder;
    }

    public function update_reminder_by_id($reminderid, stdClass $data) {
        $this->update_reminder_by_instance($this->dao->get_entity($reminderid), $data);
    }

    public function update_reminder_by_instance(reminder $reminder, stdClass $data) {

        $reminderid = $reminder->get_id();
        foreach ((array)$data as $property => $value) {
            $setter = 'set_'.$property;
            if (is_callable(array($reminder, $setter))) {
                $reminder->$setter($value);
            }
        }
        $reminder->set_id($reminderid);
        
        $this->dao->update($reminder);
    }

    public function update_reminder_by_version_id($versionid, stdClass $data) {
        $this->update_reminder_by_instance($this->get_reminder_by_version_id($versionid), $data);
    }

    public function delete_reminder_by_versionid($versionid) {
        $reminder = $this->get_reminder_by_version_id($versionid);
        if ($reminder !== false) {
            $this->delete_reminder_by_instance($reminder);
        }
    }

    public function delete_reminder_by_id($reminderid) {
        $reminder = $this->dao->get_entity($reminderid);
        $this->delete_reminder_by_instance($reminder);
    }

    public function delete_reminder_by_instance(reminder $reminder) {
        $this->dao->delete($reminder);
    }

    public function get_reminder_by_version_id($versionid) {
        return $this->dao->get_entity_where(array('versionid' => $versionid));
    }
}


