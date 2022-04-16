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

class manager
{
    /** Insert the data into our database table.
     * @param string $course_short_name_
     * @param string $course_full_name
     * @param string $title_of_forum
     * @param string $description
     * @param string $time_created
     * @param string $time_modified
     * @return bool true if successful
     */
    public function create_record(int $userid, string $course_short_name,
                                  string $course_full_name, string $title_of_forum,
                                  string $description, string $time_created,
                                  string $time_modified): bool
    {
        global $DB;

        $insert_record = new stdClass();

        $insert_record->course_short_name = $course_short_name;
        $insert_record->course_full_name = $course_full_name;
        $insert_record->title_of_forum = $title_of_forum;
        $insert_record->description = $description;
        $insert_record->time_created = $time_created;
        $insert_record->time_modified = $time_modified;
        $insert_record->userid = $userid;

        return $DB->insert_record('local_forums', $insert_record, false);

    }

    /** Update details for a single form record.
     * @return bool message data or false if not found.
     */
    public function update_records(int $id, int $userid, string $course_short_name,
                                   string $course_full_name, string $title_of_forum,
                                   string $description): bool
    {
        global $DB;

        date_default_timezone_set('Europe/Istanbul');
        $date = new DateTime('NOW');
        $date = date_format($date, 'Y-m-d h:i:s');

        $object = new stdClass();

        $object->id = $id;
        $object->$userid = $userid;
        $object->course_short_name = $course_short_name;
        $object->course_full_name = $course_full_name;
        $object->title_of_forum = $title_of_forum;
        $object->description = $description;
        $object->time_modified = $date;

        return $DB->update_record('local_forums', $object);
    }

    /** Delete a forum.
     * @param $id
     * @return bool
     * @throws \dml_transaction_exception
     * @throws dml_exception
     */
    public function delete_forums($id) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $deletedForum = $DB->delete_records('local_forums', ['id' => $id]);
        if ($deletedForum) {
            $DB->commit_delegated_transaction($transaction);
        }
        return true;
    }

    /** Get a message given an id
     *  @param $id
     *  @return object of requested record
     */
    public function get_form(int $id) {
        global $DB;
        return $DB->get_record('local_forums', ['id' => $id]);
    }
}
