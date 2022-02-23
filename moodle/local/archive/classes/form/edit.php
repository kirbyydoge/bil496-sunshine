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

        $mform->addElement('text', 'user_name', get_string('user_name', 'local_archive')); // Add elements to your form
        $mform->setType('user_name', PARAM_NOTAGS);                   //Set type of element
        $mform->setDefault('user_name', get_string('user_name', 'local_archive'));        //Default value

        $mform->addElement('text', 'user_lastname', get_string('user_lastname', 'local_archive')); // Add elements to your form
        $mform->setType('user_lastname', PARAM_NOTAGS);                   //Set type of element
        $mform->setDefault('user_lastname', get_string('user_lastname', 'local_archive'));        //Default value

        $mform->addElement('text', 'course_short_name', get_string('course_short_name', 'local_archive')); // Add elements to your form
        $mform->setType('course_short_name', PARAM_NOTAGS);           //Set type of element
        $mform->setDefault('course_short_name', get_string('course_short_name', 'local_archive'));        //Default value

        $mform->addElement('text', 'course_full_name', get_string('course_full_name', 'local_archive')); // Add elements to your form
        $mform->setType('course_full_name', PARAM_NOTAGS);          //Set type of element
        $mform->setDefault('course_full_name', get_string('course_full_name', 'local_archive'));        //Default value

        $choices = array();
        $choices['0'] = 'Quiz';
        $choices['1'] = 'Midterm Exam';
        $choices['2'] = 'Final Exam';
        $choices['3'] = 'Homework';
        $choices['4'] = 'Practice Questions';
        $choices['5'] = 'Slides';
        $choices['6'] = 'Solutions';
        $choices['7'] = 'Other';
        $mform->addElement('select', 'record_type', get_string('record_type', 'local_archive'), $choices);
        $mform->setDefault('record_type', '7');

        /*
         *  set a datetime.
         *
         *  $mform->addElement('hidden', 'time_created');
            $mform->setType('time_created', PARAM_INT);

            $mform->addElement('hidden', 'time_modified');
            $mform->setType('time_modified', PARAM_INT);
         */
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}