<?php

require_once(__DIR__."/../../config.php");
global $USER, $PAGE, $OUTPUT;
require_login();
$PAGE->set_url(new moodle_url('/local/focusmode/timer.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('timer', 'local_focusmode'));


$PAGE->set_pagelayout('embedded');
echo $OUTPUT->header();
$email=$USER->email;
$mustacheset->myarr=array($email);
$mustacheset->timer = get_string('timer', 'local_focusmode');
$mustacheset->submit = get_string('submit', 'local_focusmode');
$mustacheset->alert_time = get_string('alert_time', 'local_focusmode');
$mustacheset->minutes = get_string('minutes', 'local_focusmode');
echo $OUTPUT->render_from_template("local_focusmode/timer",$mustacheset);
echo $OUTPUT->footer();