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

require_once(__DIR__ . '/../../config.php');

global $DB, $OUTPUT, $PAGE, $CFG;

require_login();

$PAGE->set_url(new moodle_url('/local/forums/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('forums', 'local_forums'));
$PAGE->set_heading(get_string('manage_forums', 'local_forums'));
$PAGE->requires->js_call_amd('local_forums/confirm');

$forumid = required_param('id', PARAM_INT);

$forum = $DB->get_record('local_forums', ["id" => $forumid]);
$threads = $DB->get_records("local_forums_threads", ["forumid" => $forumid]);
$templatecontext = (object)[
    "addreply_url" => new moodle_url("/local/forums/addreply.php"),
    "addthread_url" => new moodle_url("/local/forums/addthread.php"),
    "threaddata_url" => new moodle_url("/local/forums/threaddata.php"),
    "forumdata_url" =>new moodle_url("/local/forums/forumdata.php")
];

echo $OUTPUT->render_from_template('local_forums/manage', $templatecontext);