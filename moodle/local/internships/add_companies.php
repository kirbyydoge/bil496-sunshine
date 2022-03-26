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
 * @package    local_internships
 * @author     Elcin Duman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $DB, $USER, $OUTPUT, $PAGE, $CFG;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/internships/classes/form/add_companies.php');
require_once($CFG->dirroot . '/local/internships/classes/company_manager.php');

require_login();
$id = optional_param('company_id',0, PARAM_INT);

$PAGE->set_url(new moodle_url('/local/archive/add_companies.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('add_company', 'local_internships'));

$mform = new add_companies();
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/internships/manage_company.php', get_string('cancelled_form', 'local_internships'));
}
else if($fromform = $mform->get_data()) {
    //manage companies over here.
    $manage_companies = new company_manager();
    $userid = $USER->id;

    if($fromform->id) { //update companies
        $manage_companies->update_company(
            $fromform->id,
            $fromform->company_name,
            $fromform->company_url,
            $fromform->userid
        );
        redirect($CFG->wwwroot . '/local/internships/manage_company.php', get_string('updated_company', 'local_internships'));
    }
    else {
        $manage_companies->create_company($fromform->company_name, $fromform->company_url, $userid);
        redirect($CFG->wwwroot . '/local/internships/manage_company.php', get_string('created_company', 'local_internships'));
    }
}

if($id) {
    $manage_companies = new company_manager();
    $internships = $manage_companies->get_information($id);
    if(!$internships) {
        throw new invalid_parameter_exception("Internship Company Not Found.");
    }
    $mform->set_data($internships);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();