<?php

require_once(__DIR__."/../../config.php");
global $USER;
require_login();
$PAGE->set_url(new moodle_url('/local/kahoot/createquestion.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('create_question', 'local_kahoot'));

$PAGE->set_pagelayout('embedded');
echo $OUTPUT->header();
$email=$USER->email;
$templatecontext->mylist=array($email);
$templatecontext->addquestion = get_string('addquestion', 'local_kahoot');
$templatecontext->submit = get_string('submit', 'local_kahoot');
$templatecontext->question = get_string('question', 'local_kahoot');
echo $OUTPUT->render_from_template("local_kahoot/createquestion",$templatecontext);
echo $OUTPUT->footer();