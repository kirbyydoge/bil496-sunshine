<?php

require_once(__DIR__."/../../config.php");
global $USER;
require_login();
$PAGE->set_url(new moodle_url('/local/kahoot/creategame.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('create_game', 'local_kahoot'));


$PAGE->set_pagelayout('embedded');
echo $OUTPUT->header();
$email=$USER->email;
$templatecontext->mylist=array($email);
$templatecontext->creategame = get_string('create_game', 'local_kahoot');
$templatecontext->create = get_string('create', 'local_kahoot');
echo $OUTPUT->render_from_template("local_kahoot/creategame",$templatecontext);
echo $OUTPUT->footer();