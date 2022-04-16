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

$PAGE->set_url(new moodle_url('/local/forums/addforum.php'));
$PAGE->set_context(\context_system::instance());

$_POST = json_decode(file_get_contents("php://input"), true);

if (empty($_POST["courseid"])) {
    echo json_encode(["result" => "COURSEID_CAN_NOT_BE_NULL"]);
    return;
}

if (empty($_POST["title"])) {
    echo json_encode(["result" => "FORUMID_CAN_NOT_BE_NULL"]);
    return;
}

$forum_manager = new forum_manager();
$courseid = $_POST["courseid"];
$title = $_POST["title"];
$id = $forum_manager->create_forum($USER->id, $courseid, $title);
echo json_encode(["id" => $id, "result" => "success"]);