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
        return $DB->get_records("mod_autograder_assignments", ["courseid" => $courseid]);
    }

    public function create_assignment(string $name, string $run_command, array $assignment_args,
                                      array $assignment_outs, int $userid, int $courseid,
                                      int $deadline) {
        global $DB;
        $assignment = new stdClass();
        $assignment->name = $name;
        $assignment->userid = $userid;
        $assignment->courseid = $courseid;
        $assignment->deadline = $deadline;
        $assignid = $DB->insert_record("mod_autograder_assignments", $assignment);
        $runcommand = new stdClass();
        $runcommand->assignid = $assignid;
        $runcommand->runcommand = $run_command;
        $commandid = $DB->insert_record("mod_autograder_runcommands", $runcommand);
        $testcases = array();
        for($i = 0; $i < count($assignment_args); $i++) {
            $arg = $assignment_args[$i];
            $out = $assignment_outs[$i];
            $testcase = new stdClass();
            $testcase->commandid = $commandid;
            $testcase->argument = $arg;
            $testcase->output = $out;
            $testcases[] = $testcase;
        }
        $DB->insert_records("mod_autograder_testcases", $testcases);
        return $assignid;
    }

    public function get_run_command(int $assignid) {
        global $DB;
        return $DB->get_record("mod_autograder_runcommands", ["assignid" => $assignid]);
    }

    public function get_testcases(int $commandid) {
        global $DB;
        $pairs = $DB->get_records("mod_autograder_testcases", ["commandid" => $commandid]);
        $testcases = array();
        foreach ($pairs as $pair) {
            $testcases[] = [
                "args" => $pair->argument,
                "outs" => [$pair->output]
            ];
        }
        return $testcases;
    }

}