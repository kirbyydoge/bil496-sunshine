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

/** This serves the usage of managing archives in Moodle.
 *
 * Version details
 *
 * @package    local_forums
 * @author     Elcin Duman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$CFG = '';
$PAGE = '';
$OUTPUT = '';

require_once(__DIR__ . '/../../config.php');

global $DB;

$PAGE->set_url(new moodle_url('/local/forums/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Forums');

echo $OUTPUT->header();
$records = $DB->get_records('local_forums');

$templatecontext = (object)[
    'records' => array_values($records),
    'editurl' => new moodle_url('/local/forums/edit.php'),
];

echo $OUTPUT->render_from_template('local_forums/manage', $templatecontext);

echo $OUTPUT->footer();