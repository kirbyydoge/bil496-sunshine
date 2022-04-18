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
require_once(__DIR__ . '/classes/course_manager.php');
require_once(__DIR__ . '/classes/forum_manager.php');
require_once(__DIR__ . '/classes/form/create_forum.php');

global $CFG, $USER, $PAGE, $OUTPUT, $SESSION;

$PAGE->set_url(new moodle_url('/local/forums/createforum.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("title_create_forum", "local_forums"));

$course_manager = new course_manager();
$forum_manager = new forum_manager();

$courses = $course_manager->user_get_courses_teaching($USER->id);
$mform = new \form\create_forum(null, array("courses" => $courses));
$data = $mform->get_data();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/forums/manage.php');
}
else if($data) {
    $courseid = $data->course_select;
    $title = $data->forum_title;
    $student_lock = $data->student_lock ? 1 : 0;
    $forumids = $forum_manager->create_forum($USER->id, $courseid, $title, $student_lock);
    redirect($CFG->wwwroot . '/local/forums/manage.php?id=' . $forumids);
}

$template_context = [
    "body_title" => get_string("title_create_forum", "local_forums"),
    "form_html" => $mform->render()
];

echo $OUTPUT->header();

echo $OUTPUT->render_from_template("local_forums/createforum", $template_context);

echo $OUTPUT->footer();