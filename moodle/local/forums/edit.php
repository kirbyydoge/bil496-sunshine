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

global $DB, $OUTPUT, $PAGE, $USER, $CFG;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/forums/classes/form/create_thread.php');
require_once($CFG->dirroot . '/local/forums/classes/forum_manager.php');

$id = optional_param("id", 0, PARAM_INT);
if(!empty($_POST['forumid'])) {
    $forumid = (int) $_POST['forumid'];
} else {
    $forumid = required_param('forumid', PARAM_INT);
}

$PAGE->set_url(new moodle_url('/local/forums/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Add a new Forum Discussion');

$mform = new create_thread(null, ["forumid" => $forumid]);

//Form processing is done here
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/forums/manage.php?id='.$forumid, 'Thread cancelled.');
} else if ($fromform = $mform->get_data()) {
    $forum_manager = new forum_manager();
    $forum_manager->create_thread($USER->id, $forumid, $fromform->title_of_forum, $fromform->description);
    redirect($CFG->wwwroot . '/local/forums/manage.php?id='.$forumid, 'Thread Submitted.');
}

$template_context = [
    "body_title" => get_string("title_edit", "local_forums"),
    "form_html" => $mform->render()
];


echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_forums/edit', $template_context);
echo $OUTPUT->footer();