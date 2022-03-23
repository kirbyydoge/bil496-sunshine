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

require_once(__DIR__ . '/../../config.php');

global $CFG, $USER, $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/mod/autograder/read.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string("title_upload", "mod_autograder"));

$fs = get_file_storage();

$fileinfo = array(
    "component" => "mod_autograder",
    "filearea" => "autograde",
    "itemid" => 0,
    "contextid" => 1,
    "filepath" => "/",
    "filename" => "POCAssignment0.java"
);

$file = $fs->get_file($fileinfo["contextid"], $fileinfo["component"], $fileinfo["filearea"],
                        $fileinfo["itemid"], $fileinfo["filepath"], $fileinfo["filename"]);

echo $OUTPUT->header();

if($file) {
    $contents = $file->get_content();
    echo $contents;
}
else {
    echo "No File.<br>";
}

echo $OUTPUT->footer();