<?php
// This file is part of Moodle Study Program Plugin
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
use function calendar_get_events;
use function enrol_get_all_users_courses;
use const SECONDS_PER_DAY;

/**
 * Version details
 *
 * @package    local_studyprogram
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once("$CFG->libdir/formslib.php");

class upload extends moodleform {

    public function definition() {
        global $USER;
        $mform = $this->_form;

        $user_events = $this->_customdata["user_events"];

        $mform->addElement("filemanager", "attachments", "Attachments", null, array(
            "subdirs" => 0, "maxbytes" => 1048576, "areamaxbytes" => 1048576, "maxfiles" => 20,
                "accepted_types" => "*", "return_types" => 2 | 1
        ));

        $this->add_action_buttons();
    }

}