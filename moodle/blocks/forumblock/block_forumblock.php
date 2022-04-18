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
 * @package   block_forumblock
 * @author    Elcin Duman
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;

class block_forumblock extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_forumblock');
    }

    function get_content() {
        global $DB, $CFG, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }
        $content = "";
        $this->content->footer = '';

        $courseid = $PAGE->course->id;
        $categoryid = ($this->page->context->contextlevel === CONTEXT_COURSECAT) ? $this->page->category->id : null;

        $forums = $DB->get_records("local_forums");
        $url = " ";
        if($courseid==1) {
            $url .= get_string('shown','block_forumblock') . '<br>';
            foreach ($forums as $fr) {
                $course = $DB->get_record("course", ["id" => $fr->courseid]);
                $content = $course->shortname . " - " . $fr->title;
                $urls = new moodle_url("/local/forums/manage.php", ["id" => $fr->id]);
                $url .= html_writer::link($urls, $content) . '<br>';
            }
            $url2 = new \moodle_url('/local/forums/createforum.php');
            $this->content->footer = html_writer::div(
                html_writer::link($url2, get_string('gotoforums', 'block_forumblock')),
            );
        } else {
            $forums = $DB->get_records("local_forums", ["courseid" => $courseid]);
            foreach($forums as $fr) {
                $content = $fr->title;
                $urls = new moodle_url("/local/forums/manage.php", ["id" => $fr->id]);
                $url .= html_writer::link($urls, $content) . '<br>';
            }
        }

        $this->content->text = $url;

        return $this->content;
    }
}

/*
 *
 */