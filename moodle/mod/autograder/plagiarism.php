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
require_once(__DIR__ . '/classes/plagiarism_checker.php');
require_once(__DIR__ . '/classes/assignment_manager.php');

global $CFG, $USER, $PAGE, $OUTPUT, $COURSE;

$context = context_course::instance($COURSE->id);

$PAGE->set_url(new moodle_url('/mod/autograder/plagiarism.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("title_plagiarism", "mod_autograder"));

$assignid = required_param('id', PARAM_INT);

$assignment_manager = new assignment_manager();
$plagiarism_checker = new plagiarism_checker();

$result = $plagiarism_checker->check_plagiarism($assignid);
$assignment = $assignment_manager->get_assignment($assignid);

$template_context = [
    "body_title" => $assignment->name,
    "form_html" => $result
];

echo $OUTPUT->header();

echo $OUTPUT->render_from_template("mod_autograder/plagiarism", $template_context);

echo $OUTPUT->footer();