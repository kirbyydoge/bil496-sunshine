<?php

require_once(__DIR__."/../../config.php");

$PAGE->set_url(new moodle_url('/local/focusmode/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Focus Mode');


echo $OUTPUT->header();
$templatecontext=(object)[
    'texttodisplay'=>'focusmode',
];
echo $OUTPUT->render_from_template("local_focusmode/focusmode",$templatecontext);
echo $OUTPUT->footer();