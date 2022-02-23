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
 * Version details
 *
 * @package    local_studyprogram
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../calendar/lib.php');

const SECONDS_PER_DAY = 86400;

function print_user_debug($userid) {
    $time_offset = 30 * SECONDS_PER_DAY;
    $start_time = time();
    $end_time = $start_time + $time_offset;

    $enrolled_courses = enrol_get_all_users_courses($userid);

    echo "<p>DEBUG: Cur userid {$userid}</p>";

    echo "<p>DEBUG: Have ".count($enrolled_courses)." enrolled course(s)</p>";

    foreach ($enrolled_courses as $course) {
        echo "<p>DEBUG: Course {$course->id}</p>";

        $course_events = calendar_get_events($start_time, $end_time, false, false, $course->id);

        foreach ($course_events as $event) {
            echo "<p>DEBUG: Course Event id:'{$event->id}' type:'{$event->eventtype}' name:'{$event->name}'</p>";
        }
    }

}