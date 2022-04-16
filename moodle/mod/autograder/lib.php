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
    global $CFG, $USER, $PAGE;
    require_once($CFG->dirroot . "/mod/autograder/classes/assignment_manager.php");
    $assignment_manager = new assignment_manager();
    $name = $data->assignment_name;
    $description = $data->assignment_desc;
    $run_command = $data->assignment_run;
    $args_list = str_getcsv($data->assignment_args);
    $outs_list = str_getcsv($data->assignment_outs);
    $points_list = str_getcsv($data->assignment_points);
    $course_id = $data->course;
    $due_date = $data->due_date;
    $contextid = $PAGE->context->id;
    $draftid = $data->attachments;
    return $assignment_manager->create_assignment(  $name, $description, $run_command,
                                                    $args_list, $outs_list, $points_list, $USER->id,
                                                    $course_id, $due_date, $contextid, $draftid );
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

function mod_autograder_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    require_login($course, true, $cm);
    $itemid = array_shift($args);
    $filename = array_pop($args);
    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_autograder', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}