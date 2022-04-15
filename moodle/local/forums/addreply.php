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
 * @author     Elcin Duman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $DB, $OUTPUT, $PAGE, $USER, $CFG;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/forums/classes/forum_manager.php');

$PAGE->set_url(new moodle_url('/local/forums/addreply.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Add a new Forum Discussion');

if(!empty($_POST["threadid"])) {
    $forum_manager = new forum_manager();
    $replyid = empty($_POST["replyid"]) ? 0 : (int) $_POST["replyid"];
    $id = $forum_manager->add_reply($USER->id, $_POST["threadid"], $_POST["reply"], $replyid);
    echo json_encode(["replyid" => $id, "status" => "success"]);
}
else {
    echo json_encode(["status" => "THREADID_CAN_NOT_BE_NULL"]);
}