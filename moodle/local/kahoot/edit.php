<?php

require_once(__DIR__."/../../config.php");
require_once($CFG->dirroot . '/local/kahoot/classes/form/edit.php');
require_once($CFG->dirroot . '/local/kahoot/classes/form/submit.php');
require_login();
$PAGE->set_url(new moodle_url('/local/kahoot/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('edit_game', 'local_kahoot'));
global $DB;
$PAGE->requires->single;
// $mfrom=new kahootedit();

// echo $OUTPUT->header();
// // $mform->display();
// $mform->display();
// echo $OUTPUT->footer();

//Instantiate simplehtml_form
$mform = new kahootedit();
$mformButton= new kahooteditbutton();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot."/local/kahoot/manage.php", get_string('cancelled_form', 'local_kahoot'));
} else if ($fromform = $mform->get_data()) {
//   var_dump($fromform);
//   die;

    $recordtoinsert=new stdClass();
    $recordtoinsert->id=4;
    $recordtoinsert->messagetext=$fromform->messagetext;
    $recordtoinsert->messagetype=$fromform->messagetype;

    $DB->insert_record('local_kahoot',$recordtoinsert);
}


$mform->set_data($toform);
echo $OUTPUT->header();
// $mform->display();
$mform->display();
// $mformButton->display();
echo $OUTPUT->footer();