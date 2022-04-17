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

class manager {

    /** Insert the data into our database table.
     * @param string $course_short_name
     * @param string $course_full_name
     * @param string $record_type
     * @param int    $date_of_the_record
     * @param string $time_created
     * @param string $time_modified
     * @param int $draftid
     * @param int $contextid
     * @param int $userid
     * @return bool true if successful
     */
    public function create_record(string $course_short_name,
                                  string $course_full_name, string $record_type, int $date_of_the_record,
                                  string $time_created, string $time_modified,
                                  int $draftid, int $contextid, int $userid): bool
    {
        global $DB;

        $insert_record = new stdClass();
        $insert_record->course_short_name = $course_short_name;
        $insert_record->course_full_name = $course_full_name;
        $insert_record->time_created = $time_created;
        $insert_record->time_modified = $time_modified;
        $insert_record->record_type = $record_type;
        $insert_record->userid = $userid;
        $insert_record->date_of_the_record = $date_of_the_record;
        $insert_record->url = "NULL";
        $insert_record->filename="NULL";

        return $this->update_records($DB->insert_record('local_archive', $insert_record,  $returnid=true, $bulk=false), $course_short_name, $course_full_name, $record_type, $date_of_the_record,
                            $draftid, $contextid, $userid);
    }

    public function generate_itemid(int $userid, int $id) {
        return $userid * 10**10 + $id;
    }

    /** Update details for a single record.
     * @param int $id the message we're trying to get.
     * @param string $course_short_name the new type for the course name.
     * @param string $course_full_name the new type for the course name.
     * @param string $record_type the new type for the record name.
     * @param int $date_of_the_record the new type for the record name.
     * @param int $draftid
     * @param int $contextid
     * @param int $userid
     * @return bool message data or false if not found.
     */
    public function update_records(int $id, string $course_short_name,
                                   string $course_full_name,
                                   string $record_type,
                                   int $date_of_the_record,
                                   int $draftid, int $contextid, int $userid): bool
    {
        global $DB;
        $url = "";
        $filenames = ""; //initializing

        date_default_timezone_set('Europe/Istanbul');
        $date = new DateTime('NOW');
        $date = date_format($date, 'Y-m-d h:i:s');

        $object = new stdClass();
        $itemid = $this->generate_itemid($userid, $id);

        $object->id = $id;
        $object->course_short_name = $course_short_name;
        $object->course_full_name = $course_full_name;
        $object->record_type = $record_type;
        $object->date_of_the_record = $date_of_the_record;
        $object->time_modified = $date;
        $object->userid = $userid;
        $object->fileid = $itemid;

        file_save_draft_area_files($draftid, $contextid, 'local_archive', 'attachment',
            $itemid, array('subdirs' => 0, 'maxbytes' => 1048576, 'maxfiles' => 20));

        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'local_archive', 'attachment', $itemid);
        $counter=0;
        $counting = count($files);

        foreach ($files as $file) {

            $filename = $file->get_filename();
            if ($filename != '.') {

                $url .= moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                        $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);

                $filenames .= $filename;

                if($counter<$counting-1) {
                    $url .= " ";
                    $filenames .= " ";
                }
                $object->url = $url;
                $object->filename = $filenames;
            }
            $counter++;
        }

        $t = $DB->update_record('local_archive', $object);
        $this->insert_url_table($contextid, $itemid);
        return $t;
    }


    public function get_urls(int $itemid) {
        global $DB;
        $sql = " SELECT id, course_short_name, fileid, filename, url FROM {local_archive} 
                WHERE fileid = :fileid";
        $params =  ["fileid" => $itemid];
        $entry = $DB->get_records_sql($sql, $params);
        return $entry;
    }

    public function insert_url_table(int $contextid, int $itemid) {

        global $DB;
        $insert_record = new stdClass();
        $update_record = new stdClass();
        $entry = $this->get_urls($itemid);
        //if we are dealing with the same itemids, we should delete and re-insert it since updating records is ambigous.
        $recs = $this->get_URL_table($itemid);

        $update = false;

        //updating the URL table is the crucial point.
        foreach($recs as $r) {
            if($r->fileid) { //fileid does existis
                $update=true;
            }
        }

        if($update) {
           foreach($recs as $r) {
               $update_record->id = $r->id;
               $update_record->filename=".";
               $update_record->url = "NULL";
               $update_record->fileid="-1";
               $DB->update_record('local_urls_table', $update_record);
           }
        }

        foreach($entry as $e) {
           $arr = explode(" ", $e->url);
           $names = explode(" ", $e->filename);
            for($i=0; $i<count($arr); $i++) {
                $insert_record->fileid = $itemid;
                $insert_record->url = $arr[$i];
                $insert_record->filename = $names[$i];
                $DB->insert_record('local_urls_table', $insert_record, $returnid=true, $bulk=false);
            }
        }
        //invalid itemids are deleted.
        if($update)
            $this->delete_urls_unnecessary($contextid,"-1");
    }

    public function join_tables(int $itemid) {
        global $DB;
        $sql = "SELECT la.id, la.course_short_name, la.fileid, la.filename, la.url
                FROM {local_archive} la
                LEFT OUTER JOIN {local_urls_table} lut ON 
                la.fileid=lut.fileid";
        $params = ['fileid'=>$itemid];
        return $DB->get_records_sql($sql, $params);
    }

    public function get_URL_table(int $itemid) {
        global $DB;
        $sql = " SELECT id, filename, fileid, url FROM {local_urls_table} 
                WHERE fileid = :fileid";
        $params =  ["fileid" => $itemid];
        $entry = $DB->get_records_sql($sql, $params);
        return $entry;
    }

    /** Delete a record.
     * @param $id
     * @return bool
     * @throws \dml_transaction_exception
     * @throws dml_exception
     */
    public function delete_message($messageid):bool {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $deletedMessage = $DB->delete_records('local_archive', ['id' => $messageid]);

        if ($deletedMessage) {
            $DB->commit_delegated_transaction($transaction);
        }

        return true;
    }

    /** Delete files in attachments.
     * @param int $contextid
     * @param int $itemid
     * @return void
     * @throws \dml_transaction_exception
     * @throws dml_exception
     */
    public function delete_files(int $contextid, int $itemid):void {
        global $DB;
        $fs = get_file_storage();

        $files = $fs->get_area_files($contextid, 'local_archive', 'attachment', $itemid);
        foreach($files as $file) {
            if($file) {
                $file->delete();
            }
        }
        $this->delete_urlstable_records($itemid);
        return;
    }

    /** Delete from urls table.
     * @param int $contextid
     * @param int $itemid
     * @return void
     * @throws \dml_transaction_exception
     * @throws dml_exception
     */
    public function delete_urlstable_records(int $itemid): void {

        global $DB;
        //deleting also from the URL Table.
        //we should get the DB table, from their fileids.
        $url_records = $this->get_URL_table($itemid);

        foreach($url_records as $recs) {
            $DB->delete_records('local_urls_table', ['id' => $recs->id]);
        }
    }

    /** Delete from urls table.
     * @param int $contextid
     * @param int $itemid
     * @return void
     * @throws \dml_transaction_exception
     * @throws dml_exception
     */
    public function delete_urls_unnecessary(int $contextid, int $itemid):void {
        global $DB;
        $sql =  "fileid = :fileid";
        $params = ["fileid" => $itemid];
        $this->delete_files($contextid, $itemid); //when a file is unused, delete those files.
        $DB->delete_records_select('local_urls_table', $sql, $params);
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