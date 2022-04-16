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
 * @package    mod_autograder
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $USER, $PAGE, $OUTPUT, $DB;

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/assignment_manager.php');
require_once(__DIR__ . '/classes/course_manager.php');
require_once(__DIR__ . '/classes/file_manager.php');

$id = required_param('id', PARAM_INT);
$action = optional_param("action", null, PARAM_TEXT);
require_login();

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'autograder');
$autograder = $DB->get_record("autograder", ["id" => $cm->instance]);
$course = $DB->get_record("course", ["id" => $autograder->course]);

$PAGE->set_context(\context_system::instance());
$PAGE->set_url(new moodle_url('/mod/autograder/view.php'));
$PAGE->set_title(get_string("title_view", "mod_autograder"));

$assignment_manager = new assignment_manager();
$course_manager = new course_manager();
$file_manager = new file_manager();

$teaching_courses = $course_manager->user_get_courses_teaching($USER->id);
$is_teacher = count(array_filter($teaching_courses, function ($course) {
    global $autograder;
    return $course->id == $autograder->course;
})) != 0;

$files = $file_manager->get_user_assignment_files($autograder->userid, $autograder->id);

$user_assignments = array();
$assignment = new stdClass();
$assignment->name = $autograder->name;
$assignment->description = $autograder->description;
$assignment->date = gmdate("d-m-Y", $autograder->deadline);
$assignment->course_name = $course->fullname;
$assignment->course_url = $CFG->wwwroot . "/course/view.php?id=" . $course->id;
$assignment->autograde_url = $CFG->wwwroot . "/mod/autograder/autograde.php?id=" . $autograder->id;
$assignment->assign_url = $CFG->wwwroot . "/mod/autograder/upload.php?id=" . $autograder->id;
$assignment->plagiarism_url = $CFG->wwwroot . "/mod/autograder/plagiarism.php?id=" . $autograder->id;
$assignment->plagiarism_name = get_string("plagiarism_name", "mod_autograder");
$assignment->autograde_name = get_string("autograde_name", "mod_autograder");
$assignment->submit_name = get_string("submit_name", "mod_autograder");
$assignment->is_teacher = $is_teacher;
if(count($files) > 0) {
    $file = $files[0];
    $assignment->file_url = $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                                                                    $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
    $assignment->file_name = $file->get_filename();
}
$user_assignments[] = $assignment;

$template_context = [
    "body_title" => get_string("title_autograde", "mod_autograder"),
    "assignments" => $user_assignments
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template("mod_autograder/deadlines", $template_context);
echo $OUTPUT->footer();