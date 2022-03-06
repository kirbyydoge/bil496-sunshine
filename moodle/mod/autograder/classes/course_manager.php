<?php
// This file is part of Moodle Study Program Plugin
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

class course_manager {

    public function user_get_courses_teaching(int $userid) {
        global $DB;
        // Get all contexts for this user, either admin (roleid 3) or teacher (roleid 5)
        $sql = "
            SELECT contextid
            FROM mdl_role_assignments
            WHERE (roleid = 3 OR roleid = 5) AND userid = :userid
        ";
        $params = [
            "userid" => $userid
        ];
        try {
            $context_objs = $DB->get_records_sql($sql, $params);
        } catch (dml_exception $e) {
            // Log error here.
            return [];
        }
        $courses = array();
        // Foreach context, get instance id of course (countextlevel 50) and retrieve related courses.
        foreach ($context_objs as $entry) {
            $sql = "
                SELECT instanceid
                FROM mdl_context
                WHERE id = :contextid AND contextlevel = 50;
            ";
            $params = [
                "contextid" => $entry->contextid
            ];
            try {
                $instance_id = $DB->get_record_sql($sql, $params)->instanceid;
            } catch (dml_exception $e) {
                // Log error here.
                continue;
            }
            $sql = "
                SELECT id, shortname, fullname
                FROM mdl_course
                WHERE id = :instanceid;
            ";
            $params = [
                "instanceid" => $instance_id
            ];
            try {
                $courses[] = $DB->get_record_sql($sql, $params);
            } catch (dml_exception $e) {
                // Log error here.
            }
        }
        return $courses;
    }

    public function create_assignment(string $name, int $userid, int $courseid, int $deadline) {
        global $DB;
        $assignment = new stdClass();
        $assignment->name = $name;
        $assignment->userid = $userid;
        $assignment->courseid = $courseid;
        $assignment->deadline = $deadline;
        return $DB->insert_record("mod_autograder_assignments", $assignment);
    }

}