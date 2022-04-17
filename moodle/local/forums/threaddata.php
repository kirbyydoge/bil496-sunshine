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

/** This serves the usage of managing archives in Moodle.
 *
 * Version details
 *
 * @package    local_forums
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $DB, $OUTPUT, $PAGE, $CFG;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . "/local/forums/locallib.php");

require_login();

$PAGE->set_url(new moodle_url('/local/forums/threaddata.php'));
$PAGE->set_context(\context_system::instance());

$_POST = json_decode(file_get_contents("php://input"), true);

function format_replies($replies) {
    $formatted = [];
    $index_table = [];
    foreach ($replies as $reply) {
        if($reply->replyid == 0) {
            $reply->replies = [];
            $index_table[$reply->id] = count($formatted);
            $formatted[] = $reply;
        }
    }
    foreach ($replies as $reply) {
        if($reply->replyid != 0) {
            $formatted[$index_table[$reply->replyid]]->replies[] = $reply;
        }
    }
    foreach ($replies as $reply) {
        $reply->username = get_user_name_by_id($reply->userid);
        unset($reply->userid);
        unset($reply->replyid);
    }
    return $formatted;
}

if(!empty($_POST["threadid"])) {
    $thread_id = $_POST["threadid"];
    $thread_info = $DB->get_record("local_forums_threads", ["id" => $thread_id]);
    $thread_info->username = get_user_name_by_id($thread_info->userid);
    unset($thread_info->userid);
    $replies = $DB->get_records("local_forums_replies", ["threadid" => $thread_id]);
    $replies = format_replies($replies);
    echo json_encode(["main" => $thread_info, "replies" => $replies, "result" => "success"]);
}
else {
    echo json_encode(["result" => "THREADID_CAN_NOT_BE_NULL"]);
}
