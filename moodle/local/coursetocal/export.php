<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->libdir.'/bennu/bennu.inc.php');

global $PAGE;

$PAGE->set_context(context_system::instance());

$userid = optional_param('userid', 0, PARAM_INT);
$username = optional_param('username', '', PARAM_TEXT);
$authtoken = required_param('authtoken', PARAM_ALPHANUM);
$generateurl = optional_param('generateurl', '', PARAM_TEXT);
$eventid = optional_param('eventid', 0, PARAM_INT);

if (empty($CFG->enablecalendarexport)) {
    die('no export');
}
$checkuserid = !empty($userid) && $user = $DB->get_record('user', array('id' => $userid), 'id,password');
$checkusername = !empty($username) && $user = $DB->get_record('user', array('username' => $username), 'id,password');
if (!$checkuserid && !$checkusername) {
    die('Invalid authentication');
}

$authuserid = !empty($userid) && $authtoken == sha1($userid . $user->password . $CFG->calendar_exportsalt);
$authusername = !empty($username) && $authtoken == sha1($username . $user->password . $CFG->calendar_exportsalt);
if (!$authuserid && !$authusername) {
    die('Invalid authentication');
}

$calendartype = \core_calendar\type_factory::get_calendar_instance();
$now = $calendartype->timestamp_to_date_array(time());
$paramcategory = false;
$limitnum = 0;

$event = \calendar_event::load($eventid);

$ical = new iCalendar;
$ical->add_property('method', 'PUBLISH');
$ical->add_property('prodid', '-//Moodle Pty Ltd//NONSGML Moodle Version ' . $CFG->version . '//EN');

$hostaddress = str_replace('http://', '', $CFG->wwwroot);
$hostaddress = str_replace('https://', '', $hostaddress);
$ev = new \iCalendar_event;
$ev->add_property('uid', $event->id.'@'.$hostaddress);
$ev->add_property('summary', format_string($event->name, true));
$description = format_text($event->description, $event->format);
$description = html_to_text($description, 0);
$ev->add_property('description', $description);

$ev->add_property('class', 'PUBLIC'); // PUBLIC / PRIVATE / CONFIDENTIAL
$ev->add_property('last-modified', Bennu::timestamp_to_datetime($event->timemodified));

if (!empty($event->location)) {
    $ev->add_property('location', $event->location);
}

$ev->add_property('dtstamp', Bennu::timestamp_to_datetime()); // now
if ($event->timeduration > 0) {
    $ev->add_property('dtstart', Bennu::timestamp_to_datetime($event->timestart)); // when event starts.
    $ev->add_property('dtend', Bennu::timestamp_to_datetime($event->timestart + $event->timeduration));
} else if ($event->timeduration == 0) {
    $ev->add_property('dtstart', Bennu::timestamp_to_datetime($event->timestart));
    $ev->add_property('dtend', Bennu::timestamp_to_datetime($event->timestart));
} else {
    throw new coding_exception("Negative duration is not supported yet.");
}

$ical->add_component($ev);

$serialized = $ical->serialize();
if(empty($serialized)) {
    // TODO
    die('bad serialization');
}

$filename = 'icalexport.ics';

header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
header('Expires: '. gmdate('D, d M Y H:i:s', 0) .'GMT');
header('Pragma: no-cache');
header('Accept-Ranges: none');
header('Content-disposition: attachment; filename='.$filename);
header('Content-length: '.strlen($serialized));
header('Content-type: text/calendar; charset=utf-8');

echo $serialized;
