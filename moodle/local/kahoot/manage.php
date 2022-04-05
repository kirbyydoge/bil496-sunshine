<?php

require_once(__DIR__."/../../config.php");

$PAGE->set_url(new moodle_url('/local/kahoot/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('kahoot game');


echo $OUTPUT->header();
$templatecontext=(object)[
    'texttodisplay'=>'Kahoot başlıyor inş',
];
echo $OUTPUT->render_from_template("local_kahoot/kahoot",$templatecontext);
echo $OUTPUT->footer();