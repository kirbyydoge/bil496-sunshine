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

class file_manager {

    public function save_draft_area(int $draftid, int $contextid, int $userid, int $assignmentid) {
        global $DB;
        $itemid = $this->generate_itemid($userid, $assignmentid);
        file_save_draft_area_files($draftid, $contextid, 'mod_autograder', 'autograde',
            $itemid, array('subdirs' => 0, 'maxbytes' => 1048576, 'maxfiles' => 50));
        $submission = new stdClass();
        $submission->userid = $userid;
        $submission->assignmentid = $assignmentid;
        $submission->fileid = $itemid;
        $DB->insert_record("mod_autograder_submissions", $submission);
    }

    public function generate_itemid(int $userid, int $assignmentid) {
        return $userid * 10**10 + $assignmentid;
    }

    public function get_user_assignment_files(int $userid, int $assignmentid) {
        global $DB;
        $fs = get_file_storage();
        $itemid = $this->generate_itemid($userid, $assignmentid);
        $sql = "
            SELECT id, contextid, component, filearea, itemid, filepath, filename
            FROM mdl_files
            WHERE itemid = :itemid AND filename != '.';
        ";
        $params = [
            "itemid" => $itemid
        ];
        $entries = $DB->get_records_sql($sql, $params);
        $files = array();
        foreach ($entries as $entry) {
            $file = $fs->get_file($entry->contextid, $entry->component, $entry->filearea,
                                    $entry->itemid, $entry->filepath, $entry->filename);
            $files[] = $file;
        }
        return $files;
    }

    public function get_assignment_files(int $assignmentid) {
        global $DB;
        $submissions = $DB->get_records("mod_autograder_submissions", ["assignmentid" => $assignmentid]);
        $all_files = array();
        foreach ($submissions as $submission) {
            $files = $this->get_user_assignment_files($submission->userid, $submission->assignmentid);
            $all_files[$submission->userid] = $files;
        }
        return $all_files;
    }

}