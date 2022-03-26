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

require_once(__DIR__ . '/../../config.php');
require_login();
global $DB, $USER, $OUTPUT, $PAGE, $CFG;

$PAGE->set_url(new moodle_url('/local/internships/manage_company.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('internships', 'local_internships'));
$PAGE->set_heading(get_string('manage_internships', 'local_internships'));

$records = $DB->get_records('local_internships_companies', null, 'id');

echo $OUTPUT->header();
$templatecontext = (object)[
    'records' => array_values($records),
    'editurl' => new moodle_url('/local/internships/add_companies.php'),
    'edit_company' => (get_string('edit_company', 'local_internships')),
    'delete_company' => (get_string('delete_company', 'local_internships')),
    'create' => get_string('create_company', 'local_internships'),
];

echo $OUTPUT->render_from_template('local_internships/company_manage', $templatecontext);
echo $OUTPUT->footer();
