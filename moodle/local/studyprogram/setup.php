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
require_once(__DIR__ . '/classes/form/setup.php');
require_once(__DIR__ . '/classes/Cardinal.php');

global $CFG, $USER, $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/local/studyprogram/setup.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("title_setup", "local_studyprogram"));

$cardinal = new Cardinal();

$user_events = $cardinal->get_user_events_sorted($USER->id);

$to_form = array("user_events" => $user_events);

$mform = new \form\setup(null, $to_form);
$data = $mform->get_data();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . "/local/studyprogram/view.php");
}
else if($data) {
    $cardinal = new Cardinal();
    $counter = 0;
    foreach ($user_events as & $event) {
        $field_name = EVENT_FIELD_TEXT . $counter++;
        $event[EVENT_STUDY_WIDTH] = $data->$field_name * SECONDS_PER_DAY;
    }
    $study_program = $cardinal->analyze_user_dates_advanced($user_events, SETUP_DEF_DAYS * SECONDS_PER_DAY);
    $cardinal->cleanup_studyprogram($USER->id);
    $cardinal->create_studyprogram($USER->id, $study_program);
    redirect($CFG->wwwroot . "/local/studyprogram/view.php");
}

$template_context = [
    "body_title" => get_string("title_setup", "local_studyprogram"),
    "form_html" => $mform->render()
];

echo $OUTPUT->header();

echo $OUTPUT->render_from_template("local_studyprogram/setup", $template_context);

echo $OUTPUT->footer();