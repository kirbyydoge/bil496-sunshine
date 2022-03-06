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
        $fm = new file_manager();
        $fs = get_file_storage();
        $file_map = $fm->get_assignment_files($assignmentid);
        foreach ($file_map as $key => $value) {
            $user_entry = $DB->get_record("user", ["id" => $key]);
            $username = $user_entry->firstname . " " . $user_entry->lastname;
            $this->autograde_single_assignment($username, $value, $main, $test_cases);
        }
    }

    private function autograde_single_assignment(string $username, array $files, string $main, array $test_cases) {
        $this->write_files(self::SRC_PATH, $files);
        $this->compile_files(self::SRC_PATH, self::BIN_PATH);
        echo "Autograding user: " . $username . "<br>";
        $this->run_program(self::BIN_PATH, $main, $test_cases);
        $this->cleanup(self::SRC_PATH);
        $this->cleanup(self::BIN_PATH);
    }

    private function write_files($path, $files) {
        foreach ($files as $file) {
            $cur_file = fopen($path . "/" . $file->get_filename(), "w");
            fwrite($cur_file, $file->get_content());
            fclose($cur_file);
        }
    }

    private function compile_files($src_path, $compile_path) {
        exec("javac -d ". $compile_path . " " . $src_path . "/*.java 2>&1", $output, $result);
        foreach ($output as $item) {
            echo $item . "<br>";
        }
    }

    private function run_program($class_path, $main, $test_cases) {
        foreach ($test_cases as $test_case) {
            $args = $test_case["args"];
            $outs = $test_case["outs"];
            $test_result = $this->run_test($class_path, $main, $args, $outs);
        }
    }

    private function run_test($class_path, $main, $args, $outs) {
        echo "Running With Args: " . $args. " ";
        exec("java -cp ". $class_path . "; ". $main . " " . $args . " 2>&1", $output, $result);
        $test_result = $outs === $output;
        if($test_result) {
            echo "(Test Passed) <br>";
        }
        else {
            echo "(Test Failed) <br>";
            echo "\tProgram Output:<br>";
            foreach ($output as $line) {
                echo "\t" . $line . "<br>";
            }
            echo "\tExpected Output:<br>";
            foreach ($outs as $line) {
                echo "\t" . $line . "<br>";
            }
        }
        return $test_result;
    }

    private function cleanup($folder_path) {
        $files = glob($folder_path . "/*"); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file);
            }
        }
    }

}