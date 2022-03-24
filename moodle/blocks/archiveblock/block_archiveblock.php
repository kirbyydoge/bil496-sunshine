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
 * @package   block_archiveblock
 * @author    Elcin Duman
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_archiveblock extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_archiveblock');
    }

    function get_content() {
        global $DB, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $courses = $DB->get_records('local_archive');
        $counter = 0;
        $content = "";

        foreach($courses as $cs) {

            $content .= $cs->course_short_name;

            if($cs->record_type==0) {
                $content .= get_string('quiz', 'block_archiveblock');
            } else if($cs->record_type==1) {
                $content .= get_string('midterm_exam', 'block_archiveblock');
            } else if($cs->record_type==2) {
                $content .= get_string('final_exam', 'block_archiveblock');
            } else if($cs->record_type==3) {
                $content .= get_string('homework', 'block_archiveblock');
            } else if($cs->record_type==4) {
                $content .= get_string('practice_questions', 'block_archiveblock');
            } else if($cs->record_type==5) {
                $content .= get_string('slides', 'block_archiveblock');
            } else if($cs->record_type==6) {
                $content .= get_string('solutions', 'block_archiveblock');
            } else if($cs->record_type==7) {
                $content .= get_string('other', 'block_archiveblock');
            }
            $content .= get_string('added', 'block_archiveblock') . '<br>';
            $arr[$counter] = $content;
            $counter +=1;
            $content = "";
        }

        //latest three records will be shown. If wanted, you can show more.
        $this->content = new stdClass;
        $this->content->text = $arr[$counter-1] . $arr[$counter-2] . $arr[$counter-3] ;

        $url = new \moodle_url('/local/archive/manage.php');
        $this->content->footer = html_writer::div(
            html_writer::link($url, get_string('gotoarchives', 'block_archiveblock')),
        );

        return $this->content;
    }
}
