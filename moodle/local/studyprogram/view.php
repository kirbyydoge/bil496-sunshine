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

// Temporary localization function until moodle support is implemented.
function get_string_temp($query) {
    $string = array();
    $string["view_title"] = 'Çalışma Programı Görüntüle';
    if (in_array($query, $string)) {
        return $string[$query];
    }
    return $query;
}

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../calendar/lib.php');
require_once(__DIR__ . '/view_debug.php');
require_once(__DIR__ . '/lib.php');

$PAGE->set_url(new moodle_url('/local/studyprogram/view.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string_temp("view_title"));

const SECONDS_PER_DAY = 86400;
const DEBUG = true;

//Get timestamp of current second and the second 30 days after.
$time_offset = 30 * SECONDS_PER_DAY;
$start_time = time();
$end_time = $start_time + $time_offset;
$study_width = 10 * SECONDS_PER_DAY; //Minimum required days of work per due date.

//Get user's enrolled courses.
$enrolled_courses = enrol_get_all_users_courses($USER->id);
$user_events = array();

//For each course, get due dates from calendar API.
foreach ($enrolled_courses as $course) {
    $course_events = calendar_get_events($start_time, $end_time, false, false, $course->id);
    $user_events[$course->id] = array();
    foreach ($course_events as $event) {
        if($event->eventtype == "due") {
            $user_events[] = [$event->id, $event->name, $event->timestart];
        }
    }
}

echo $OUTPUT->header();

echo '<h1>Still in development...</h1>';

//Remove null elements.
$user_events = array_filter($user_events, function ($val, $key) {
    return !is_null($val[2]);
}, ARRAY_FILTER_USE_BOTH);

//Sort due dates from earliest to latest.
usort($user_events, function ($a, $b) {
    $a_time = $a[2];
    $b_time = $b[2];
    return ($a_time < $b_time) ? -1 : (($a_time > $b_time) ? 1 : 0);
});

if(DEBUG) {
    $study_dates = analyze_user_dates_advanced($user_events, $study_width);
    foreach ($study_dates as $advice) {
        echo "<p>Start studying for ".str_replace("is due", "", $advice["name"])." at ".date("d/m/y", $advice["start_date"])." </p>";
    }
}

echo $OUTPUT->footer();