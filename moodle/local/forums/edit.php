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
global $DB, $OUTPUT, $PAGE, $USER;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/forums/classes/form/edit.php');
require_once($CFG->dirroot . '/local/forums/classes/manager.php');

$id = optional_param('forumid',0, PARAM_INT);

$PAGE->set_url(new moodle_url('/local/forums/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Add a new Forum Discussion');

$mform = new edit();

//Form processing is done here
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/forums/manage.php', 'Form Discussion is cancelled.');

} else if ($fromform = $mform->get_data()) {

    $manager = new manager();
    $userid = $USER->id;

    if ($fromform->id) {
        $manager->update_records(
            $fromform->id,
            $userid,
            $fromform->course_short_name,
            $fromform->course_full_name,
            $fromform->title_of_forum,
            $fromform->description
        );
        redirect($CFG->wwwroot . '/local/forums/manage.php', get_string('updated_record', 'local_forums'));
    }
    else {
        $manager->create_record($userid, $fromform->course_short_name, $fromform->course_full_name,
            $fromform->title_of_forum, $fromform->description,
            $fromform->time_created, $fromform->time_modified);
        redirect($CFG->wwwroot . '/local/forums/manage.php', 'Forum discussion has been submitted.');
    }
}

if($id) {
    $manager = new manager();
    $forum = $manager->get_form($id);
    if (!$forum) {
        throw new invalid_parameter_exception("Form Not Found.");
    }
    $mform->set_data($forum);
}


echo $OUTPUT->header();
$mform->display();

echo $OUTPUT->footer();