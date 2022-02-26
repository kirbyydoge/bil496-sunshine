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
 * @package    local_studyprogram
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../calendar/lib.php');
require_once(__DIR__ . '/view_debug.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/classes/Cardinal.php');

global $CFG, $USER, $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/local/studyprogram/view.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("view_title", "local_studyprogram"));

const DEBUG = false;

$cardinal = new Cardinal();

//Get timestamp of current second and the second 30 days after.
$time_offset = 30 * SECONDS_PER_DAY;
$start_time = time();
$end_time = $start_time + $time_offset;
$study_width = 10 * SECONDS_PER_DAY; //Minimum required days of work per due date.

//Get user's enrolled courses.
$enrolled_courses = enrol_get_all_users_courses($USER->id);
$user_events = array();

//For each course, get due dates from calendar API
foreach ($enrolled_courses as $course) {
    $course_events = calendar_get_events($start_time, $end_time, false, false, $course->id);
    $user_events[$course->id] = array();
    foreach ($course_events as $event) {
        if($event->eventtype == "due") {
            $user_events[] = [
                "id" => $event->id,
                "courseid" => $course->id,
                "name" => $event->name,
                "timestart" => $event->timestart
            ];
        }
    }
}

//Remove null elements.
$user_events = array_filter($user_events, function ($val, $key) {
    return array_key_exists("timestart", $val);
}, ARRAY_FILTER_USE_BOTH);

//Sort due dates from earliest to latest.
usort($user_events, function ($a, $b) {
    $a_time = $a["timestart"];
    $b_time = $b["timestart"];
    return ($a_time < $b_time) ? -1 : (($a_time > $b_time) ? 1 : 0);
});

$study_dates = $cardinal->analyze_user_dates_advanced($user_events, $study_width);

foreach ($study_dates as $advice) {
    //$cardinal->create_studyprogram_event($USER->id, $advice);
    continue;
}

echo $OUTPUT->header();

$template_context = [
    "body_title" => get_string("view_title", "local_studyprogram"),
    "name_setup" => get_string("view_btn_setup", "local_studyprogram"),
    "url_setup" => new moodle_url("/local/studyprogram/setup.php")
];

echo $OUTPUT->render_from_template("local_studyprogram/view", $template_context);

echo $OUTPUT->footer();