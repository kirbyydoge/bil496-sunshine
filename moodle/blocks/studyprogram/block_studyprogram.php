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

/**
 * Form for editing HTML block instances.
 *
 * @package   block_studyprogram
 * @author    Elcin Duman
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_studyprogram extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_studyprogram');
    }
    function get_content() {
        global $DB, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }

     //   $courses = $DB->get_records('local_studyprogram');

        $this->content = new stdClass;
        $url = new \moodle_url('/local/studyprogram/view.php');
        $this->content->footer = html_writer::div(
            html_writer::link($url, get_string('go_to_study_program', 'block_studyprogram')),
        );
        return $this->content;
    }
}