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

class plagiarism_checker {

    private const SRC_PATH = __DIR__ . "/../cache/moss";
    private const BIN_PATH = __DIR__ . "/../cache/bin";

    public function check_plagiarism(int $assignmentid) {
        global $DB;
        $fm = new file_manager();
        $file_map = $fm->get_assignment_files($assignmentid);
        $this->cache_files($file_map, plagiarism_checker::SRC_PATH);
        $this->cleanup(plagiarism_checker::SRC_PATH);
        return null;
    }

    private function call_moss($args) {
        //TODO:
        //Register moss
        //Store moss.pl somewhere inside plugin (!!USER'S MOSS ID SHOULD BE ENCRYPTED)
        //Call moss function from here and return the link to user
    }

    private function cache_files($file_map, $path) {
        global $DB;
        foreach ($file_map as $key => $value) {
            $user_entry = $DB->get_record("user", ["id" => $key]);
            $username = $user_entry->firstname . " " . $user_entry->lastname;
            $user_path = $path . "/" . $username . "_" . $key;
            mkdir($user_path, 0777, true);
            $this->write_files($user_path, $value);
        }
    }

    private function write_files($path, $files) {
        foreach ($files as $file) {
            $cur_file = fopen($path . "/" . $file->get_filename(), "w");
            fwrite($cur_file, $file->get_content());
            fclose($cur_file);
        }
    }

    private function cleanup($path) {
        $files = glob($path . "/");
        foreach ($files as $file) {
           is_dir($file) ? $this->cleanup_recursive($file) : unlink($file);
        }
    }

    private function cleanup_recursive($path) {
        $files = glob($path . "/");
        foreach ($files as $file) {
            is_dir($file) ? $this->cleanup($file) : unlink($file);
        }
        rmdir($path);
    }

}