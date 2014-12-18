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

namespace mod_sequentialdocuments\task;

defined('MOODLE_INTERNAL') || die();

include_once __DIR__.'/../model/dao/reminder_dao.php';
include_once __DIR__.'/../model/dao/version_dao.php';
include_once __DIR__.'/../../locallib.php';

class send_reminders extends \core\task\scheduled_task {
    public function get_name() {
        // Shown in admin screen.
        return get_string('sendreminders', 'mod_sequentialdocuments');
    }

    public function execute() {
        $reminderdao = new \reminder_dao();
        $versiondao = new \version_dao();
        $reminders = $reminderdao->get_all_entities();

        $now = time();
        foreach ($reminders as $reminder) {

            $version = $versiondao->get_entity($reminder->get_versionid());
            if ($version->get_duevalidated()) {
                continue;
            }

            $duetime = $reminder->get_duetime();

            if ($now > $duetime) {

                $lastpost = $reminder->get_lastpost();
                $count = $reminder->get_postcount();
                if ($count < $reminder->get_postneeded() &&
                        ($lastpost == -1 || $this->is_older_than_a_week($lastpost))) {

                    $this->send_message(
                                $reminder,
                                get_string('versionduelatesubject', 'mod_sequentialdocuments'),
                                $this->get_postdueday_message($reminder)
                    );
                    $reminder->set_postcount($count + 1);
                    $reminder->set_lastpost($now);
                }

            } else {

                // TODO: refactor this.
                // Yes, the following code(-smell) *does* work.
                $messagesent = false;
                $diff = ~floor(($now - $duetime) / 86400);
                switch (true) {

                    case ($diff == 0):
                        if ($reminder->get_duedaysent()) {
                            $messagesent = true;

                        } else if (!$messagesent && $reminder->get_dueday() && !$reminder->get_duedaysent()) {
                            $this->send_message(
                                        $reminder,
                                        get_string('versionduesubject', 'mod_sequentialdocuments'),
                                        $this->get_dueday_message($reminder)
                            );
                            $messagesent = true;
                        }
                        $reminder->set_duedaysent(true);

                    case ($diff <= 1):
                        if ($reminder->get_onedaysent()) {
                            $messagesent = true;

                        } else if (!$messagesent && $reminder->get_oneday() && !$reminder->get_onedaysent()) {
                            $this->send_message(
                                        $reminder,
                                        get_string('versionduesubject', 'mod_sequentialdocuments'),
                                        $this->get_oneday_message($reminder)
                            );
                            $messagesent = true;
                        }
                        $reminder->set_onedaysent(true);

                    case ($diff <= 7):
                        if ($reminder->get_oneweeksent()) {
                            $messagesent = true;

                        } else if (!$messagesent && $reminder->get_oneweek() && !$reminder->get_oneweeksent()) {
                            $this->send_message(
                                        $reminder,
                                        get_string('versionduesubject', 'mod_sequentialdocuments'),
                                        $this->get_oneweek_message($reminder)
                            );
                            $messagesent = true;
                        }
                        $reminder->set_oneweeksent(true);

                    case ($diff <= 14):
                        if ($reminder->get_twoweekssent()) {
                            $messagesent = true;

                        } else if (!$messagesent && $reminder->get_twoweeks() && !$reminder->get_twoweekssent()) {
                            $this->send_message(
                                        $reminder,
                                        get_string('versionduesubject', 'mod_sequentialdocuments'),
                                        $this->get_twoweeks_message($reminder)
                            );
                            $messagesent = true;
                        }
                        $reminder->set_twoweekssent(true);

                    case ($diff <= 31):
                        if ($reminder->get_onemonthsent()) {
                            $messagesent = true;

                        } else if (!$messagesent && $reminder->get_onemonth() && !$reminder->get_onemonthsent()) {
                            $this->send_message(
                                        $reminder,
                                        get_string('versionduesubject', 'mod_sequentialdocuments'),
                                        $this->get_onemonth_message($reminder)
                            );
                            $messagesent = true;
                        }
                        $reminder->set_onemonthsent(true);
                        break;
                }
            }

            $reminderdao->update($reminder);
        }
    }

    protected function send_message($reminder, $subject, $message) {
        $eventdata = new \stdClass();
        $eventdata->component         = 'mod_sequentialdocuments';
        $eventdata->name              = 'duedatereminder';
        $eventdata->userfrom          = $reminder->get_senderid();
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = '';
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = $message;
        $eventdata->smallmessage      = '';
        $eventdata->notification      = 1;


        $students = sequentialdocuments_get_instance_students($reminder->get_instanceid());
        foreach ($students as $student) {
            $eventdata->userto = $student;
            message_send($eventdata);
        }
    }

    protected function is_older_than_a_week($timestamp) {
        return $timestamp <= (time() - 604800); // (60 * 60 * 24 * 7) = 604 800 seconds = one week.
    }

    protected function get_postdueday_message($reminder) {
        $url = get_view_version_url($reminder->get_versionid(), $reminder->get_instanceid());
        return get_string('versionduelate', 'mod_sequentialdocuments').
                ' <a href="'.$url.'">'.get_string('versionduelink', 'mod_sequentialdocuments').'</a>';
    }

    protected function get_dueday_message($reminder) {
        $url = get_view_version_url($reminder->get_versionid(), $reminder->get_instanceid());
        return get_string('versionduedueday', 'mod_sequentialdocuments').
                ' <a href="'.$url.'">'.get_string('versionduelink', 'mod_sequentialdocuments').'</a>';
    }

    protected function get_oneday_message($reminder) {
        $url = get_view_version_url($reminder->get_versionid(), $reminder->get_instanceid());
        return get_string('versiondueoneday', 'mod_sequentialdocuments').
                ' <a href="'.$url.'">'.get_string('versionduelink', 'mod_sequentialdocuments').'</a>';
    }

    protected function get_oneweek_message($reminder) {
        $url = get_view_version_url($reminder->get_versionid(), $reminder->get_instanceid());
        return get_string('versiondueoneweek', 'mod_sequentialdocuments').
                ' <a href="'.$url.'">'.get_string('versionduelink', 'mod_sequentialdocuments').'</a>';
    }

    protected function get_twoweeks_message($reminder) {
        $url = get_view_version_url($reminder->get_versionid(), $reminder->get_instanceid());
        return get_string('versionduetwoweeks', 'mod_sequentialdocuments').
                ' <a href="'.$url.'">'.get_string('versionduelink', 'mod_sequentialdocuments').'</a>';
    }

    protected function get_onemonth_message($reminder) {
        $url = get_view_version_url($reminder->get_versionid(), $reminder->get_instanceid());
        return get_string('versiondueonemonth', 'mod_sequentialdocuments').
                ' <a href="'.$url.'">'.get_string('versionduelink', 'mod_sequentialdocuments').'</a>';
    }
}


