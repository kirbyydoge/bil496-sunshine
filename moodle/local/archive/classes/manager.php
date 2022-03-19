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
 * @package    local_archive
 * @author     Elcin Duman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use dml_exception;
use stdClass;

class manager {

    /** Insert the data into our database table.
     * @param string $user_name
     * @param string $user_lastname
     * @param string $course_short_name
     * @param string $course_full_name
     * @param string $record_type
     * @param int    $date_of_the_record
     * @param string $time_created
     * @param string $time_modified
     * @return bool true if successful
     */
    public function create_record(string $user_name,
                                  string $user_lastname,
                                  string $course_short_name,
                                  string $course_full_name, string $record_type, int $date_of_the_record,
                                  string $time_created, string $time_modified,
                                  int $draftid, int $contextid, int $userid, int $assignmentid): bool
    {
        global $DB;

        $insert_record = new stdClass();
        $itemid = $this->generate_itemid($userid, $assignmentid);

        file_save_draft_area_files($draftid, $contextid, 'local_archive', 'attachment',
            $itemid, array('subdirs' => 0, 'maxbytes' => 1048576, 'maxfiles' => 50));

        $insert_record->user_name = $user_name;
        $insert_record->user_lastname = $user_lastname;
        $insert_record->course_short_name = $course_short_name;
        $insert_record->course_full_name = $course_full_name;
        $insert_record->record_type = $record_type;
        $insert_record->date_of_the_record = $date_of_the_record;
        $insert_record->time_created = $time_created;
        $insert_record->time_modified = $time_modified;
        $insert_record->userid = $userid;
        $insert_record->assignmentid = $assignmentid;
        $insert_record->fileid = $itemid;

        return $DB->insert_record('local_archive', $insert_record, false);
    }


    public function generate_itemid(int $userid, int $assignmentid) {
        return $userid * 10**10 + $assignmentid;
    }

    /** Update details for a single record.
     * @param int $id the message we're trying to get.
     * @param string $user_name the new text for the user name.
     * @param string $user_lastname the new type for the user lastname.
     * @param string $course_short_name the new type for the course name.
     * @param string $course_full_name the new type for the course name.
     * @param string $record_type the new type for the record name.
     * @param int $date_of_the_record the new type for the record name.
     * @return bool message data or false if not found.
     */
    public function update_records(int $id, string $user_name,
                                   string $user_lastname,
                                   string $course_short_name,
                                   string $course_full_name,
                                   string $record_type,
                                   int $date_of_the_record,
                                   int $draftid, int $contextid, int $userid, int $assignmentid): bool
    {
        global $DB;
        date_default_timezone_set('Europe/Istanbul');
        $date = new DateTime('NOW');
        $date = date_format($date, 'Y-m-d h:i:s');

        $object = new stdClass();
        $itemid = $this->generate_itemid($userid, $id);

        file_save_draft_area_files($draftid, $contextid, 'local_archive', 'attachment',
            $itemid, array('subdirs' => 0, 'maxbytes' => 1048576, 'maxfiles' => 20));

        $object->id = $id;
        $object->user_name = $user_name;
        $object->user_lastname = $user_lastname;
        $object->course_short_name = $course_short_name;
        $object->course_full_name = $course_full_name;
        $object->record_type = $record_type;
        $object->date_of_the_record = $date_of_the_record;
        $object->time_modified = $date;
        $object->userid = $userid;
        $object->assignmentid = $assignmentid;
        $object->fileid = $itemid;

        return $DB->update_record('local_archive', $object);
    }

    /** Delete a record.
     * @param $id
     * @return bool
     * @throws \dml_transaction_exception
     * @throws dml_exception
     */
    public function delete_message($messageid)
    {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $deletedMessage = $DB->delete_records('local_archive', ['id' => $messageid]);
        if ($deletedMessage) {
            $DB->commit_delegated_transaction($transaction);
        }
        return true;
    }

    /** Get an archive record given an id
     *  @param $id
     *  @return object of requested record
     */
    public function get_record(int $id) {

        global $DB;
        return $DB->get_record('local_archive', ['id' => $id]);
    }
}