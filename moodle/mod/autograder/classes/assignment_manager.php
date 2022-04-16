<?php
// This file is part of Moodle Autograder Plugin
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
 * Version details
 *
 * @package    mod_autograder
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

class assignment_manager {

    public function get_all_user_autograder_assignments($userid) {
        $user_courses = enrol_get_all_users_courses($userid, true);
        $assignments = array();
        $cur_time = time();
        foreach ($user_courses as $course) {
            $course_assignments = $this->get_autograder_assignments_by_courseid($course->id);
            foreach ($course_assignments as $assignment) {
                if($assignment->deadline > $cur_time) {
                    $assignments[] = $assignment;
                }
            }
        }
        return $assignments;
    }

    public function get_autograder_assignments_by_courseid($courseid) {
        global $DB;
        return $DB->get_records("autograder", ["course" => $courseid]);
    }

    public function create_assignment(string $name, string $description, string $run_command, array $assignment_args,
                                      array $assignment_outs, array $assignment_points, int $userid, int $courseid,
                                      int $deadline, int $contextid, int $draftid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/calendar/lib.php");
        require_once($CFG->dirroot . "/mod/autograder/classes/file_manager.php");
        $file_manager = new file_manager();

        //Add Instance
        $assignment = new stdClass();
        $assignment->name = $name;
        $assignment->description = $description;
        $assignment->userid = $userid;
        $assignment->course = $courseid;
        $assignment->deadline = $deadline;
        $assignment->fileid = 0;
        $assignid = $DB->insert_record("autograder", $assignment);
        $runcommand = new stdClass();
        $runcommand->assignid = $assignid;
        $runcommand->runcommand = $run_command;
        $commandid = $DB->insert_record("autograder_runcommands", $runcommand);
        $testcases = array();
        for($i = 0; $i < count($assignment_args); $i++) {
            $arg = $assignment_args[$i];
            $out = $assignment_outs[$i];
            $points = $assignment_points[$i];
            $testcase = new stdClass();
            $testcase->commandid = $commandid;
            $testcase->argument = $arg;
            $testcase->output = $out;
            $testcase->points = $points;
            $testcases[] = $testcase;
        }
        $DB->insert_records("autograder_testcases", $testcases);
        //Handle Calendar Event
        $event = new stdClass();
        $event->eventtype = "assignment"; // Constant defined somewhere in your code - this can be any string value you want. It is a way to identify the event.
        $event->type = CALENDAR_EVENT_TYPE_ACTION; // This is used for events we only want to display on the calendar, and are not needed on the block_myoverview.
        $event->name = $name . " is due";
        $event->description = "Homework is due";
        $event->format = FORMAT_HTML;
        $event->courseid = $courseid;
        $event->groupid = 0;
        $event->userid = 0;
        $event->modulename = 0;
        //$event->modulename = "mod_autograder"; // Setting modulename makes these events invisible for some reason.
        $event->instance = $assignid;
        $event->timestart = $deadline;
        $event->timesort = $deadline;
        $event->visible = true;
        $event->timeduration = 0;
        $db_entry = calendar_event::create($event);

        //Handle Draft Files
        $file_manager->save_draft_area($draftid, $contextid, $userid, $assignid, false);
        return $assignid;
    }

    public function delete_assignment(int $assignid) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/calendar/lib.php');
        $runcommands = $DB->get_records("autograder_runcommands", ["assignid" => $assignid]);
        try {
            $transaction = $DB->start_delegated_transaction();
            foreach ($runcommands as $command) {
                $DB->delete_records("autograder_testcases", ["commandid" => $command->id]);
            }
            $DB->delete_records("autograder_runcommands", ["assignid" => $assignid]);
            $DB->delete_records("autograder_submissions", ["assignmentid" => $assignid]);
            $transaction->allow_commit();
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        $calendars = $DB->get_records("event", ["instance" => $assignid]);
        foreach ($calendars as $calendar) {
            try {
                $event = calendar_event::load($calendar->id);
                $event->delete(true);
            }
            catch(dml_missing_record_exception $e) {
                // Another entity (user/admin/another plugin) has removed this event. No longer valid.
                continue;
            }
        }
        return true;
    }

    public function get_assignment(int $assignid) {
        global $DB;
        return $DB->get_record("autograder", ["id" => $assignid]);
    }

    public function get_run_command(int $assignid) {
        global $DB;
        return $DB->get_record("autograder_runcommands", ["assignid" => $assignid]);
    }

    public function get_testcases(int $commandid) {
        global $DB;
        $tuples = $DB->get_records("autograder_testcases", ["commandid" => $commandid]);
        $testcases = array();
        foreach ($tuples as $tuple) {
            $formatted = preg_split("/\r\n|\n|\r/", $tuple->output);
            $testcases[] = [
                "args" => $tuple->argument,
                "outs" => $formatted,
                "points" => $tuple->points
            ];
        }
        return $testcases;
    }

}