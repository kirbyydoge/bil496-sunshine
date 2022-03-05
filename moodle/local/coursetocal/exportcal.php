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
 * Confirm self registered user.
 *
 * @package    local_coursetocal
 * @copyright  2020 LMS Doctor
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

global $USER, $DB;

$password = $DB->get_record('user', array('id' => $USER->id), 'password');
$params = array();
$params['userid']      = $USER->id;
$params['authtoken']   = sha1($USER->id . (isset($password->password) ? $password->password : '') . $CFG->calendar_exportsalt);
$params['eventid']   = optional_param('eventid', 0, PARAM_INT);

$link = new moodle_url('/local/coursetocal/export.php', $params);
$urlclasses = array('class' => 'generalbox calendarurl');
$calendarurl = html_writer::tag( 'div', get_string('calendarurl', 'calendar', $link->out()), $urlclasses);
redirect($link);
