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
require_once(__DIR__ . '/view_debug.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/classes/Cardinal.php');
require_once(__DIR__ . '/classes/form/setup.php');

global $CFG, $USER, $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/local/studyprogram/view.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("title_view", "local_studyprogram"));

echo $OUTPUT->header();

$template_context = [
    "body_title" => get_string("title_view", "local_studyprogram"),
    "name_setup" => get_string("btn_view_setup", "local_studyprogram"),
    "url_setup" => new moodle_url("/local/studyprogram/setup.php")
];

echo $OUTPUT->render_from_template("local_studyprogram/view", $template_context);

echo $OUTPUT->footer();