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
 * @package    local_forums
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class course_manager {

    public function user_get_courses_teaching(int $userid) {
        global $DB;
        // Get all contexts for this user, either admin (roleid 3) or teacher (roleid 5)
        $sql = "
            SELECT *
            FROM mdl_role_assignments AS ra
            LEFT JOIN mdl_context AS ctx ON ctx.id = ra.contextid
            LEFT JOIN mdl_course AS crs ON crs.id = ctx.instanceid
            WHERE   ra.roleid < 5
            AND     ctx.contextlevel = 50
            AND     ra.userid = :userid;
        ";
        $params = [
            "userid" => $userid
        ];
        try {
            $courses = $DB->get_records_sql($sql, $params);
        } catch (dml_exception $e) {
            // Log error here.
            return [];
        }
        return $courses;
    }

}