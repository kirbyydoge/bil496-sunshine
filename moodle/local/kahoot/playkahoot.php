<?php

require_once(__DIR__."/../../config.php");
global $USER;
require_login();
$PAGE->set_url(new moodle_url('/local/kahoot/playkahoot.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('title', 'local_kahoot'));

$PAGE->set_pagelayout('embedded');
echo $OUTPUT->header();
$email=$USER->email;
$templatecontext->mylist=array($email);
echo $OUTPUT->render_from_template("local_kahoot/playkahoot",$templatecontext);
echo $OUTPUT->footer();