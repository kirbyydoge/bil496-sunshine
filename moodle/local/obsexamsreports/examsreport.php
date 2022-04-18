<?php

require_once(__DIR__."/../../config.php");
global $USER;
$PAGE->set_url(new moodle_url('/local/obsexamsreports/examsreport.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('OBS Reports');

// $PAGE->set_pagelayout('embedded');
$courseID=optional_param('id', 0, PARAM_INT);;
$templatecontext->mylist=array($courseID);
echo $OUTPUT->render_from_template("local_obsexamsreports/obsexams",$templatecontext);