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
 * @package    local_internships
 * @author     Elcin Duman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use dml_exception;
use stdClass;

class company_manager {

    /** Insert the data in the company database.
     * @param string $company_name
     * @param string $company_url
     * @param int $userid
     * @return bool true if successful
     */
    public function create_company(string $company_name, string $company_url, int $userid): bool {

        global $DB;
        $insert = new stdClass();

        $insert->company_name = $company_name;
        $insert->company_url = $company_url;
        $insert->user_id = $userid;

        return $DB->insert_record('local_internships_companies', $insert);
    }

    /** Update the requested data in the company database.
     * @param int $id
     * @param string $company_name
     * @param string $company_url
     * @param int $userid
     * @return bool true if successful
     */
    public function update_company(int $id, string $company_name, string $company_url, int $userid): bool {

        global $DB;
        $update = new stdClass();

        $update->id = $id;
        $update->company_name = $company_name;
        $update->company_url = $company_url;
        $update->user_id = $userid;

        return $DB->update_record('local_internships_companies', $update);
    }

    /** Get information about the internship company
     *  @param $id
     *  @return object of requested record
     */
    public function get_information($id) {
        global $DB;
        return $DB->get_record('local_internships_companies', ['id' => $id]);
    }

}