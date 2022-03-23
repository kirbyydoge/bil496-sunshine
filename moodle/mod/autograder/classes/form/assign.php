<?php
// This file is part of Moodle Autograder Plugin
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

namespace form;
use moodleform;

/**
 * Version details
 *
 * @package    mod_autograder
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once("$CFG->libdir/formslib.php");
require_once(__DIR__ . "/../course_manager.php");

class assign extends moodleform {

    public function definition() {
        global $USER;
        $mform = $this->_form;
        $courses = $this->_customdata["courses"];
        $choices = array();
        foreach ($courses as $course) {
            $choices[$course->id] = $course->fullname;
        }
        $mform->addElement("select", "course_select",
            get_string("course_select","mod_autograder"), $choices);
        $mform->setDefault("course_select", $courses[0]->id);
        $mform->addElement("text", "assignment_name", get_string("assignment_name", "mod_autograder"));
        $mform->addElement("text", "assignment_run", get_string("assignment_run", "mod_autograder"));
        $mform->addElement("text", "assignment_args", get_string("assignment_args", "mod_autograder"));
        $mform->addElement("text", "assignment_outs", get_string("assignment_outs", "mod_autograder"));
        $mform->addElement('date_time_selector', 'due_date', get_string("due_date", "mod_autograder"));
        $this->add_action_buttons();
    }

}