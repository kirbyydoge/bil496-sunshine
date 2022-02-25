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

$id = optional_param('id',0, PARAM_INT);
echo $id;
$archive = $DB->get_record('local_archive', array('id'=>$id), '*');

$PAGE->set_url(new moodle_url('/local/archive/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Add a new Archive Record');

$mform = new edit(null, array('id' => $archive->id));
$mform->set_data($archive);

//Form processing is done here
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/archive/manage.php', 'Archive Form is cancelled.');

} else if ($fromform = $mform->get_data()) {

    $manager = new manager();

    $DB->update_record('local_archive', $fromform);

    if ($fromform->id) {

        $manager->update_records($fromform->id, $fromform->user_name, $fromform->user_lastname, $fromform->course_short_name, $fromform->course_full_name, $fromform->record_type, $fromform->date_of_the_record);

        $courses = $DB->get_records('local_archive');
        foreach($courses as $cs) {
            $content .= $cs->course_short_name . '<br>';
        }
        echo $content;
        echo $archive->id . '<br>';
        echo $archive->course_full_name . '<br>';
        var_dump();
        die();
        redirect($CFG->wwwroot . '/local/archive/manage.php', get_string('updated_record', 'local_archive'));
    }

    $manager->create_record($fromform->user_name, $fromform->user_lastname,
                           $fromform->course_short_name, $fromform->course_full_name,
                           $fromform->record_type, $fromform->date_of_the_record,
                           $fromform->time_created, $fromform->time_modified);

    redirect($CFG->wwwroot . '/local/archive/manage.php', 'Archive Record has been submitted.');

}

echo $OUTPUT->header();

$templatecontext = (object)[

];

echo $OUTPUT->render_from_template('local_archive/edit', $templatecontext);
$mform->display();

echo $OUTPUT->footer();
