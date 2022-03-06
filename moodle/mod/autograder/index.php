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

$PAGE->set_url(new moodle_url('/mod/autograder/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("title_view", "mod_autograder"));

$assignment_manager = new assignment_manager();
$user_assignments = $assignment_manager->get_all_user_autograder_assignments($USER->id);

$autograder = new autograder();

$mform = new \form\index(null, array("assignments" => $user_assignments));
$data = $mform->get_data();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/mod/autograder/index.php');
}

$template_context = [
    "body_title" => get_string("title_assign", "mod_autograder"),
    "form_html" => $mform->render()
];

echo $OUTPUT->header();

echo $OUTPUT->render_from_template("mod_autograder/index", $template_context);

if($data) {
    $assignmentid = $data->autograde_select;
    $res = $autograder->autograde_assignment($assignmentid);
    foreach ($res as $user) {
       foreach ($user as $file) {
           echo $file->get_content() . "<br>";
       }
    }
}
echo $OUTPUT->footer();