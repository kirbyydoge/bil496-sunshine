<?php
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
