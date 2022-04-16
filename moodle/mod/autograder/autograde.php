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

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/autograder.php');
require_once(__DIR__ . '/classes/assignment_manager.php');
require_once(__DIR__ . '/classes/course_manager.php');
require_once(__DIR__ . '/classes/file_manager.php');

global $CFG, $USER, $PAGE, $OUTPUT;
$PAGE->set_url(new moodle_url('/mod/autograder/autograde.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("title_upload", "mod_autograder"));

require_login();
$assignid = required_param('id', PARAM_INT);

$assignment_manager = new assignment_manager();
$autograder = new autograder();
$course_manager = new course_manager();
$file_manager = new file_manager();

$teaching_courses = $course_manager->user_get_courses_teaching($USER->id);
$assignment = $assignment_manager->get_assignment($assignid);

$is_student = count(array_filter($teaching_courses, function ($course) {
    global $assignment;
    return $course->id == $assignment->courseid;
})) == 0;

$runobject = $assignment_manager->get_run_command($assignid);
$testcases = $assignment_manager->get_testcases($runobject->id);
if($is_student) {
    $student_cases = [$testcases[0]];
    $out_buffer = $autograder->autograde_single_user($USER->id, $assignid, $runobject->runcommand, $student_cases);
}
else {
    $student_cases = array();
    $student_cases[] = $testcases[0];
    $out_buffer = $autograder->autograde_assignment($assignid, $runobject->runcommand, $testcases);
}

$template_context = [
    "body_title" => $assignment->name,
    "out_buffer" => $out_buffer
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template("mod_autograder/autograde", $template_context);
echo $OUTPUT->footer();