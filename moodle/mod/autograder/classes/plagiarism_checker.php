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

require_once(__DIR__ . "/moss.php");
require_once(__DIR__ . "/file_manager.php");

class plagiarism_checker {

    private const SRC_PATH = __DIR__ . "/../cache/moss";
    private const USER_ID = 437334535;  //THROW-AWAY USER-ID. Needs to be encrypted and stored in DB for a few users.

    public function check_plagiarism(int $assignmentid) {
        global $DB;
        $fm = new file_manager();
        $this->cleanup(plagiarism_checker::SRC_PATH);
        $file_map = $fm->get_assignment_files($assignmentid);
        $this->cache_files($file_map, plagiarism_checker::SRC_PATH);
        $result = trim($this->call_moss(plagiarism_checker::SRC_PATH,plagiarism_checker::USER_ID, "java",
                                    "Test"));
        $this->cleanup(plagiarism_checker::SRC_PATH);
        $rows = $this->post_process_rows($result);
        $data = $this->post_process_names($rows);
        return $data;
    }

    private function call_moss($path, $moss_userid, $language, $comment) {
        $moss = new moss($moss_userid);
        $moss->setLanguage($language);
        $moss->addByWildcard($path . "/*/*");
        $moss->setCommentString($comment);
        return $moss->send();
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
        $files = glob($path . "/*");
        foreach ($files as $file) {
           is_dir($file) ? $this->cleanup_recursive($file) : unlink($file);
        }
    }

    private function cleanup_recursive($path) {
        $files = glob($path . "/*");
        foreach ($files as $file) {
            is_dir($file) ? $this->cleanup_recursive($file) : unlink($file);
        }
        rmdir($path);
    }

    private function post_process_rows($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0");
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $html = curl_exec($curl);
        curl_close($curl);
        $dom = new domDocument;
        @$dom->loadHTML($html);
        $tables = $dom->getElementsByTagName("table");
        $tbl_rows = $tables->item(0)->getElementsByTagName("tr");
        $rows = [];
        foreach ($tbl_rows as $row) {
            $cols = $row->getElementsByTagName("td");
            if (count($cols) == 0) {
                continue;
            }
            $rows[] = [
                "first" => $cols[0]->textContent,
                "second" => $cols[1]->textContent,
                "lines" => $cols[2]->textContent,
                "match_url" => $cols[0]->getElementsByTagName("a")[0]->getAttribute("href")
            ];
        }
        return $rows;
    }

    private function post_process_names($rows) {
        global $CFG;
        $data = [];
        $matches = [];
        $pattern = '/cache\/moss\/(.*?)_([0-9]+)\/(.*?) \(([0-9]+)%\)/';
        foreach ($rows as $row) {
            $cur_data = [];
            $first_file = $row["first"];
            $second_file = $row["second"];
            preg_match($pattern, $first_file, $matches);
            $cur_data["first_name"] = str_replace("_", " ", $matches[1]);
            $cur_data["first_id"] = $matches[2];
            $cur_data["first_moodle_url"] = $CFG->wwwroot . "/user/profile.php?id=" . $matches[2];
            $cur_data["first_file"] = $matches[3];
            $cur_data["first_percent"] = $matches[4];
            preg_match($pattern, $second_file, $matches);
            $cur_data["second_name"] = str_replace("_", " ", $matches[1]);
            $cur_data["second_id"] = $matches[2];
            $cur_data["second_moodle_url"] = $CFG->wwwroot . "/user/profile.php?id=" . $matches[2];
            $cur_data["second_file"] = $matches[3];
            $cur_data["second_percent"] = $matches[4];
            $cur_data["lines"] = $row["lines"];
            $cur_data["match_url"] = $row["match_url"];
            $data[] = $cur_data;
        }
        return $data;
    }

}