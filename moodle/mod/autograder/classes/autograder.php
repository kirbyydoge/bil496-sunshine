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

require_once (__DIR__ . "/file_manager.php");

class autograder {

    private const SRC_PATH = __DIR__ . "/../cache/src";
    private const BIN_PATH = __DIR__ . "/../cache/bin";

    public function autograde_assignment(int $assignmentid, string $main, array $test_cases) {
        global $DB;
        $this->cleanup(self::SRC_PATH);
        $this->cleanup(self::BIN_PATH);
        $fm = new file_manager();
        $file_map = $fm->get_assignment_files($assignmentid);
        $out_buffer = array();
        $max_points = 0;
        foreach ($test_cases as $test_case) {
            $max_points += $test_case["points"];
        }
        foreach ($file_map as $key => $value) {
            $user_entry = $DB->get_record("user", ["id" => $key]);
            $username = $user_entry->firstname . " " . $user_entry->lastname;
            $user_buffer = array();
            $points = $this->autograde_single_assignment($username, $value, $main, $test_cases, $max_points, $user_buffer);
            $out_buffer[] = [
                "username" => $username,
                "result" => $user_buffer,
                "points" => $points
            ];
        }
        return $out_buffer;
    }

    public function autograde_single_user(int $userid, int $assignmentid, string $main, array $test_cases) {
        global $DB;
        $this->cleanup(self::SRC_PATH);
        $this->cleanup(self::BIN_PATH);
        $fm = new file_manager();
        $files = $fm->get_user_assignment_files($userid, $assignmentid);
        $out_buffer = array();
        $user_entry = $DB->get_record("user", ["id" => $userid]);
        $username = $user_entry->firstname . " " . $user_entry->lastname;
        $user_buffer = array();
        $points = $this->autograde_single_assignment($username, $files, $main, $test_cases, $user_buffer);
        $out_buffer[] = [
            "username" => $username,
            "result" => $user_buffer,
            "points" => $points
        ];
        return $out_buffer;
    }

    private function autograde_single_assignment(string $username, array $files, string $main,
                                                    array $test_cases, array &$out_buffer) {
        $this->write_files(self::SRC_PATH, $files);
        $this->compile_files(self::SRC_PATH, self::BIN_PATH, $out_buffer);
        $out_buffer[] = "Autograding user: " . $username;
        $points = $this->run_program(self::BIN_PATH, $main, $test_cases, $out_buffer);
        $this->cleanup(self::SRC_PATH);
        $this->cleanup(self::BIN_PATH);
        return $points;
    }

    private function write_files($path, $files) {
        foreach ($files as $file) {
            $cur_file = fopen($path . "/" . $file->get_filename(), "w");
            fwrite($cur_file, $file->get_content());
            fclose($cur_file);
        }
    }

    private function compile_files($src_path, $compile_path, array &$out_buffer) {
        exec("javac -d ". $compile_path . " " . $src_path . "/*.java 2>&1", $output, $result);
        foreach ($output as $item) {
            $out_buffer[] = $item;
        }
    }

    private function run_program($class_path, $main, $test_cases, array &$out_buffer) {
        $total_points = 0;
        foreach ($test_cases as $test_case) {
            $args = $test_case["args"];
            $outs = $test_case["outs"];
            $points = $test_case["points"];
            $result = $this->run_test($class_path, $main, $args, $outs, $out_buffer);
            if($result) {
                $total_points += $points;
            }
        }
        return $total_points;
    }

    private function run_test($class_path, $main, $args, $outs, array &$out_buffer) {
        $out_buffer[] =  "Running With Args: " . $args;
        exec("java -cp ". $class_path . "; ". $main . " " . $args . " 2>&1", $output, $result);
        $test_result = $outs === $output;
        if($test_result) {
            $out_buffer[] = "(Test Passed)";
        }
        else {
            $out_buffer[] = "(Test Failed)\n";
            $out_buffer[] = "\tProgram Output:\n";
            foreach ($output as $line) {
                $out_buffer[] = "\t" . $line;
            }
            $out_buffer[] = "\tExpected Output:\n";
            foreach ($outs as $line) {
                $out_buffer[] = "\t" . $line;
            }
        }
        return $test_result;
    }

    private function cleanup($folder_path) {
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        $files = glob($folder_path . "/*"); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file);
            }
        }
    }

}