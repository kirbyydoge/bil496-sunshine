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
 * @package    local_archive
 * @author     Elcin Duman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$CFG = '';
$PAGE = '';
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/archive/classes/form/edit.php');

require_login();

global $DB, $USER, $OUTPUT, $PAGE;

$PAGE->set_url(new moodle_url('/local/archive/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('archives', 'local_archive'));
$PAGE->set_heading(get_string('manage_records', 'local_archive'));
$PAGE->requires->js_call_amd('local_archive/confirm');


$records = $DB->get_records('local_archive', null, 'id');

foreach($records as $r) {
    $fs = get_file_storage();
    $files = $fs->get_area_files($PAGE->context->id, 'local_archive', 'attachment', $r->fileid);
    foreach ($files as $file) {
        $filename = $file->get_filename();
        $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
            $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
        $urls[] = $url;
        $file_names[] = $filename;
        $out[] = html_writer::link($url, $filename);
    }
}

echo $OUTPUT->header();

$templatecontext = (object)[
    'records' => array_reverse(array_values($records)),
    'editurl' => new moodle_url('/local/archive/edit.php'),
    'edit_record' => (get_string('edit_record', 'local_archive')),
    'delete_record' => (get_string('delete_record', 'local_archive')),
    'create' => get_string('create_record', 'local_archive'),
    'list' => get_string('list_of_records', 'local_archive'),
];

echo $OUTPUT->render_from_template('local_archive/manage', $templatecontext);
echo $OUTPUT->footer();