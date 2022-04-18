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
 * @package    local_forums
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class forum_manager {

    public function create_forum(int $userid, int $courseid, string $title, int $student_lock) {
        global $DB;
        $entry = new stdClass();
        $entry->userid = $userid;
        $entry->courseid = $courseid;
        $entry->title = $title;
        $entry->studentlock = $student_lock;
        $entry->timecreate = date_format(new DateTime("NOW"), 'Y-m-d h:i:s');
        return $DB->insert_record("local_forums", $entry);
    }

    public function create_thread(int $userid, int $forumid, string $title, string $description) {
        global $DB;
        $entry = new stdClass();
        $entry->userid = $userid;
        $entry->forumid = $forumid;
        $entry->title = $title;
        $entry->description = $description;
        $entry->timecreate = date_format(new DateTime("NOW"), 'Y-m-d h:i:s');
        return $DB->insert_record("local_forums_threads", $entry);
    }

    public function add_reply(int $userid, int $threadid, string $reply, int $replyid = 0) {
        global $DB;
        $entry = new stdClass();
        $entry->userid = $userid;
        $entry->threadid = $threadid;
        $entry->replyid = $replyid;
        $entry->reply = $reply;
        $entry->timecreate = date_format(new DateTime("NOW"), 'Y-m-d h:i:s');
        return $DB->insert_record("local_forums_replies", $entry);
    }

}