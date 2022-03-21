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

/** This serves the usage of editing archives form in Moodle.
 *
 * Version details
 *
 * @package    local_archive
 * @author     Elcin Duman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");

class edit extends moodleform {

    //Add elements to form

    public function definition() {

        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!,

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'course_short_name', get_string('course_short_name', 'local_archive')); // Add elements to your form
        $mform->setType('course_short_name', PARAM_NOTAGS);           //Set type of element
        $mform->setDefault('course_short_name', get_string('course_short_name', 'local_archive'));        //Default value

        $mform->addElement('text', 'course_full_name', get_string('course_full_name', 'local_archive')); // Add elements to your form
        $mform->setType('course_full_name', PARAM_NOTAGS);          //Set type of element
        $mform->setDefault('course_full_name', get_string('course_full_name', 'local_archive'));        //Default value

        $choices = array();

        $choices['0'] = get_string('quiz', 'local_archive');
        $choices['1'] = get_string('midterm_exam', 'local_archive');
        $choices['2'] = get_string('final_exam', 'local_archive');
        $choices['3'] = get_string('homework', 'local_archive');
        $choices['4'] = get_string('practice_questions', 'local_archive');
        $choices['5'] = get_string('slides', 'local_archive');
        $choices['6'] = get_string('solutions', 'local_archive');
        $choices['7'] = get_string('other', 'local_archive');

        $mform->addElement('select', 'record_type', get_string('record_type', 'local_archive'), $choices);
        $mform->setDefault('record_type', '7');

        $mform->addElement('text', 'date_of_the_record', get_string('date_of_the_record', 'local_archive')); // Add elements to your form
        $mform->setType('date_of_the_record', PARAM_INT);          //Set type of element
        $mform->setDefault('date_of_the_record', get_string('date_of_the_record', 'local_archive'));

        date_default_timezone_set('Europe/Istanbul');
        $date = new DateTime('NOW');
        $date = date_format($date, 'Y-m-d h:i:s');

        $mform->addElement('hidden', 'time_created');
        $mform->setType('time_created', PARAM_NOTAGS);
        $mform->setDefault('time_created', $date);

        $mform->addElement('hidden', 'time_modified');
        $mform->setType('time_modified', PARAM_NOTAGS);
        $mform->setDefault('time_modified', $date);

        $mform->addElement("filemanager", "attachments",  get_string('attachment', 'local_archive'), null, array(
            "subdirs" => 0, "maxbytes" => 1048576, "areamaxbytes" => 1048576, "maxfiles" => 20,
            "accepted_types" => "*", "return_types" => 2 | 1));

        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}