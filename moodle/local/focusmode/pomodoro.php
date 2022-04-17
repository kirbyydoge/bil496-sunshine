<?php

require_once(__DIR__."/../../config.php");
global $USER, $PAGE, $OUTPUT;
require_login();
$PAGE->set_url(new moodle_url('/local/focusmode/pomodoro.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('p_timer', 'local_focusmode'));


$PAGE->set_pagelayout('embedded');
echo $OUTPUT->header();
$email=$USER->email;
$mustacheset->myarr=array($email);
$mustacheset->p_title = get_string('p_timer', 'local_focusmode');
$mustacheset->start = get_string('start', 'local_focusmode');
$mustacheset->reset = get_string('reset', 'local_focusmode');
$mustacheset->stop = get_string('stop', 'local_focusmode');
$mustacheset->last_session = get_string('last_session', 'local_focusmode');
$mustacheset->studied = get_string('studied', 'local_focusmode');
$mustacheset->thirty = get_string('thirty', 'local_focusmode');
$mustacheset->minutes = get_string('minutes', 'local_focusmode');
echo $OUTPUT->render_from_template("local_focusmode/pomodoro",$mustacheset);
echo $OUTPUT->footer();