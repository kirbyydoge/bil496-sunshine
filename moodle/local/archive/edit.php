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
 * @package    local_archive
 * @author     Elcin Duman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$CFG = '';
$PAGE = '';
$OUTPUT = ''; //initialized the values.
global $DB;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/archive/classes/form/edit.php');
require_once($CFG->dirroot . '/local/archive/classes/manager.php');

$id = optional_param('recordid',0, PARAM_INT);

$PAGE->set_url(new moodle_url('/local/archive/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Add a new Archive Record');

$mform = new edit();

//Form processing is done here
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/archive/manage.php', 'Archive Form is cancelled.');
} else if ($fromform = $mform->get_data()) {
    $manager = new manager();
    if ($fromform->id) {
        $manager->update_records(
            $fromform->id,
            $fromform->user_name,
            $fromform->user_lastname,
            $fromform->course_short_name,
            $fromform->course_full_name,
            $fromform->record_type,
            $fromform->date_of_the_record
        );
        redirect($CFG->wwwroot . '/local/archive/manage.php', get_string('updated_record', 'local_archive'));
    }
    else {
        /*
        $manager->create_record($fromform->user_name, $fromform->user_lastname,
            $fromform->course_short_name, $fromform->course_full_name,
            $fromform->record_type, $fromform->date_of_the_record,
            $fromform->time_created, $fromform->time_modified);*/
        redirect($CFG->wwwroot . '/local/archive/manage.php', 'Archive Record has been submitted.');
    }
}

if($id) {
    $manager = new manager();
    $archive = $manager->get_archive($id);
    if (!$archive) {
        throw new invalid_parameter_exception("Archive Not Found.");
    }
    $mform->set_data($archive);
}

echo $OUTPUT->header();

$templatecontext = (object)[

];

echo $OUTPUT->render_from_template('local_archive/edit', $templatecontext);
$mform->display();

echo $OUTPUT->footer();
