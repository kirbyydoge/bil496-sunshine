<?php

require_once(__DIR__."/../../config.php");
global $USER;
$PAGE->set_url(new moodle_url('/local/focusmode/timer.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Timer');


$PAGE->set_pagelayout('embedded');
echo $OUTPUT->header();
$email=$USER->email;
$mustacheset->myarr=array($email);
echo $OUTPUT->render_from_template("local_focusmode/timer",$mustacheset);
echo $OUTPUT->footer();