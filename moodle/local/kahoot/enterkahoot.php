<?php

require_once(__DIR__."/../../config.php");
global $USER;
require_login();
$PAGE->set_url(new moodle_url('/local/kahoot/enterkahoot.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('enter_game', 'local_kahoot'));

$PAGE->set_pagelayout('embedded');
echo $OUTPUT->header();
$email=$USER->email;
$templatecontext->mylist=array($email);
$templatecontext->entergameid = get_string('enter_game_id', 'local_kahoot');
$templatecontext->enter = get_string('enter', 'local_kahoot');
echo $OUTPUT->render_from_template("local_kahoot/enterkahoot",$templatecontext);
echo $OUTPUT->footer();