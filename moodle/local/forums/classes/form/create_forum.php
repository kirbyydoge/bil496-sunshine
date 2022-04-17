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
 * @package    local_forums
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once("$CFG->libdir/formslib.php");
require_once(__DIR__ . "/../course_manager.php");

class create_forum extends moodleform {

    public function definition() {
        global $USER;
        $mform = $this->_form;
        $courses = $this->_customdata["courses"];
        $choices = array();
        foreach ($courses as $course) {
            $choices[$course->id] = $course->fullname;
        }
        $mform->addElement("select", "course_select",
            get_string("course_select","local_forums"), $choices);
        $mform->setDefault("course_select", $courses[0]->id);
        $mform->addElement("text", "forum_title", get_string("forum_title", "local_forums"));
        $this->add_action_buttons();
    }

}