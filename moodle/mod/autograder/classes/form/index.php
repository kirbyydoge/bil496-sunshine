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

class index extends moodleform {

    public function definition() {
        global $USER;
        $mform = $this->_form;
        $assignments = $this->_customdata["assignments"];
        $choices = array();
        foreach ($assignments as $assignment) {
            $choices[$assignment->id] = $assignment->name;
        }
        $mform->addElement("select", "autograde_select",
            get_string("autograde_select","mod_autograder"), $choices);
        $mform->setDefault("autograde_select", $assignments[0]->id);
        $this->add_action_buttons(get_string("autograde", "mod_autograder"));
    }

}