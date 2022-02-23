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

/**
 * Version details
 *
 * @package    local_studyprogram
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Temporary localization function until moodle support is implemented.
function get_string_temp($query) {
    $string = array();
    $string["view_title"] = 'Çalışma Programı Görüntüle';
    if (in_array($query, $string)) {
        return $string[$query];
    }
    return $query;
}

require_once(__DIR__ . '/../../config.php');
$PAGE->set_url(new moodle_url('/local/studyprogram/view.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string_temp("view_title"));

echo $OUTPUT->header();

$echo '<h1>Still in development...</h1>';

echo $OUTPUT->footer();