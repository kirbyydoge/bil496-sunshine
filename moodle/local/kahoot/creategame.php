<?php

require_once(__DIR__."/../../config.php");
global $USER;
$PAGE->set_url(new moodle_url('/local/kahoot/creategame.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Kahoot Game Create Game');


$PAGE->set_pagelayout('embedded');
echo $OUTPUT->header();
$email=$USER->email;
$templatecontext->mylist=array($email);
echo $OUTPUT->render_from_template("local_kahoot/creategame",$templatecontext);
echo $OUTPUT->footer();