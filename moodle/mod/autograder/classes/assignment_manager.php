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

}