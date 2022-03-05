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
require_once(__DIR__ . '/classes/form/upload.php');

global $CFG, $USER, $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/local/autograder/upload.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("title_upload", "local_autograder"));

$mform = new \form\upload();
$data = $mform->get_data();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . "/local/autograder/index.php");
}
else if($data) {
    file_save_draft_area_files($data->attachments, $PAGE->context->id, 'local_autograder', 'attachments',
        0, array('subdirs' => 0, 'maxbytes' => 1048576, 'maxfiles' => 50));
    redirect($CFG->wwwroot . "/local/autograder/index.php");
}

$template_context = [
    "body_title" => get_string("title_upload", "local_autograder"),
    "form_html" => $mform->render()
];

echo $OUTPUT->header();

echo $OUTPUT->render_from_template("local_autograder/upload", $template_context);

echo $OUTPUT->footer();