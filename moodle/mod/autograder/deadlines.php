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
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/classes/form/index.php');
require_once(__DIR__ . '/classes/assignment_manager.php');
require_once(__DIR__ . '/classes/autograder.php');

global $CFG, $USER, $PAGE, $OUTPUT, $SESSION;

$PAGE->set_url(new moodle_url('/mod/autograder/deadlines.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("title_view", "mod_autograder"));

require_login();

$assignment_manager = new assignment_manager();
$course_manager = new course_manager();

$teaching_courses = $course_manager->user_get_courses_teaching($USER->id);
$user_assignments = $assignment_manager->get_all_user_autograder_assignments($USER->id);

echo $OUTPUT->header();

for($i = 0; $i < count($user_assignments); $i++) {
    $assign = $user_assignments[$i];
    $course = get_course($assign->courseid);
    $is_teacher = count(array_filter($teaching_courses, function ($course) {
        global $assign;
        return $course->id == $assign->courseid;
    })) != 0;
    $user_assignments[$i]->date = gmdate("d-m-Y", $assign->deadline);
    $user_assignments[$i]->course_name = $course->fullname;
    $user_assignments[$i]->course_url = $CFG->wwwroot . "/course/view.php?id=" . $course->id;
    $user_assignments[$i]->autograde_url = $CFG->wwwroot . "/mod/autograder/autograde.php?id=" . $assign->id;
    $user_assignments[$i]->assign_url = $CFG->wwwroot . "/mod/autograder/upload.php?id=" . $assign->id;
    $user_assignments[$i]->plagiarism_url = $CFG->wwwroot . "/mod/autograder/plagiarism.php?id=" . $assign->id;
    $user_assignments[$i]->plagiarism_name = get_string("plagiarism_name", "mod_autograder");
    $user_assignments[$i]->autograde_name = get_string("autograde_name", "mod_autograder");
    $user_assignments[$i]->submit_name = get_string("submit_name", "mod_autograder");
    $user_assignments[$i]->is_teacher = $is_teacher;
}

$template_context = [
    "body_title" => get_string("title_autograde", "mod_autograder"),
    "assignments" => $user_assignments
];
echo $OUTPUT->render_from_template("mod_autograder/deadlines", $template_context);

echo $OUTPUT->footer();
