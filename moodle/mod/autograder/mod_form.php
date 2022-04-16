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

/**
 * Version details
 *
 * @package    mod_autograder
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

global $CFG;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . "/mod/autograder/classes/course_manager.php");

class mod_autograder_mod_form extends moodleform_mod {

    public function definition() {
        global $CFG, $COURSE, $DB, $PAGE, $USER;
        $mform = $this->_form;
        $mform->addElement("text", "assignment_name", get_string("assignment_name", "mod_autograder"));
        $mform->addElement("textarea", "assignment_desc", get_string("assignment_desc", "mod_autograder"));
        $mform->addElement("textarea", "assignment_run", get_string("assignment_run", "mod_autograder"));
        $mform->addElement("textarea", "assignment_args", get_string("assignment_args", "mod_autograder"));
        $mform->addElement("textarea", "assignment_outs", get_string("assignment_outs", "mod_autograder"));
        $mform->addElement("text", "assignment_points", get_string("assignment_points", "mod_autograder"));
        $mform->addElement('date_time_selector', 'due_date', get_string("due_date", "mod_autograder"));
        $mform->addElement("filemanager", "attachments", "Attachments", null, array(
            "subdirs" => 0, "maxbytes" => 1048576, "areamaxbytes" => 1048576, "maxfiles" => 20,
            "accepted_types" => "*", "return_types" => 2 | 1
        ));
        $this->standard_coursemodule_elements();
        $this->apply_admin_defaults();
        $this->add_action_buttons();
    }

}