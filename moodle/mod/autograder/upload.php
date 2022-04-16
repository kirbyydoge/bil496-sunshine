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
require_once(__DIR__ . '/classes/form/upload.php');
require_once(__DIR__ . '/classes/assignment_manager.php');
require_once(__DIR__ . '/classes/file_manager.php');

global $CFG, $USER, $PAGE, $OUTPUT, $DB;

$PAGE->set_url(new moodle_url('/mod/autograder/upload.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("title_upload", "mod_autograder"));

require_login();

if(!empty($_POST["assignid"])) {
    $assignid = $_POST["assignid"];
}
else {
    $assignid = required_param('id', PARAM_INT);
}

$autograder = $DB->get_record("autograder", ["id" => $assignid]);
$assignment_manager = new assignment_manager();
$user_assignments = $assignment_manager->get_all_user_autograder_assignments($USER->id);

$file_manager = new file_manager();
$mform = new \form\upload();
$data = $mform->get_data();

if ($mform->is_cancelled()) {
    redirect(new moodle_url("/course/view.php", ["id" => $autograder->course]));
}
else if($data->assignid){
    $draftid = $data->attachments;
    $contextid = $PAGE->context->id;
    $userid = $USER->id;
    $assignmentid = $data->assignid;
    $file_manager->save_draft_area($draftid, $contextid, $userid, $assignmentid);
    redirect(new moodle_url("/course/view.php", ["id" => $autograder->course]));
}

if($assignid) {
    $assignment = $assignment_manager->get_assignment($assignid);
    $data = new stdClass();
    $data->assignid = $assignid;
    $mform->set_data($data);
}
else {
    $assignment = [
        "name" => "unkown",
        "id" => null
    ];
}

$template_context = [
    "body_title" => $assignment->name,
    "form_html" => $mform->render()
];

echo $OUTPUT->header();

echo $OUTPUT->render_from_template("mod_autograder/upload", $template_context);

echo $OUTPUT->footer();