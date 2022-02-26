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
require_once(__DIR__ . "/../../constants.php");
require_once($CFG->dirroot . "/calendar/lib.php");

class setup extends moodleform {

    public function definition() {
        global $USER;
        $mform = $this->_form;

        $user_events = $this->_customdata["user_events"];

        $counter = 0;
        $err_msg = get_string("err_numeric", "local_studyprogram");
        foreach ($user_events as $event) {
            $field_name = EVENT_FIELD_TEXT . $counter++;
            $mform->addElement("text", $field_name, $event[EVENT_NAME]);
            $mform->setType("$field_name", PARAM_INT);
            $mform->setDefault("$field_name", SETUP_DEF_DAYS);
            $mform->addRule($field_name, $err_msg, "numeric", null, "client");
        }

        $this->add_action_buttons();
    }

}