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
 * @package    local_forums
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once("$CFG->libdir/formslib.php");

class add_reply extends moodleform {

    //Add elements to form

    public function definition() {

        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!,

        $mform->addElement('hidden', 'threadid');
        $mform->setType('threadid', PARAM_INT);
        $mform->setDefault("threadid", $this->_customdata["threadid"]);

        $mform->addElement('hidden', 'replyid');
        $mform->setType('replyid', PARAM_INT);
        $mform->setDefault("replyid", $this->_customdata["replyid"]);

        $mform->addElement('textarea', 'reply', get_string('reply', "local_forums"), 'wrap="virtual" rows="20" cols="50"');

        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}