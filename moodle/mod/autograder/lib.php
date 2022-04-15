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

function autograder_add_instance(stdClass $data, mod_autograder_mod_form $form = null) {
    global $CFG, $USER;
    require_once($CFG->dirroot . "/mod/autograder/classes/assignment_manager.php");
    $assignment_manager = new assignment_manager();
    $name = $data->assignment_name;
    $run_command = $data->assignment_run;
    $args_list = str_getcsv($data->assignment_args);
    $outs_list = str_getcsv($data->assignment_outs);
    $course_id = $data->course;
    $due_date = $data->due_date;
    return $assignment_manager->create_assignment($name, $run_command, $args_list, $outs_list, $USER->id, $course_id, $due_date);
}

function autograder_delete_instance($id) {
    global $DB, $CFG;
    require_once($CFG->dirroot . "/mod/autograder/classes/assignment_manager.php");
    $assignment_manager = new assignment_manager();
    list ($course, $cm) = get_course_and_cm_from_cmid($id, 'autograder');
    echo $cm->instance;
    return $assignment_manager->delete_assignment($cm->instance);
}

function autograder_get_coursemodule_info($coursemodule) {
    global $DB;
    $dbparams = array('id'=>$coursemodule->instance);
    $fields = 'id, name';
    if (! $assignment = $DB->get_record('autograder', $dbparams, $fields)) {
        return false;
    }
    $result = new cached_cm_info();
    $result->name = $assignment->name;
    $result->iconurl = new moodle_url("/mod/autograder/theme/icon.svg");
    return $result;
}