<?php

require_once(__DIR__."/../../config.php");
global $USER;
$PAGE->set_url(new moodle_url('/local/focusmode/pomodoro.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Pomodoro Timer');


$PAGE->set_pagelayout('embedded');
echo $OUTPUT->header();
$email=$USER->email;
$mustacheset->myarr=array($email);
echo $OUTPUT->render_from_template("local_focusmode/pomodoro",$mustacheset);
echo $OUTPUT->footer();