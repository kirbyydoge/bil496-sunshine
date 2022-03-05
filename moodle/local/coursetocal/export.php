<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Confirm self registered user.
 *
 * @package    local_coursetocal
 * @copyright  2020 LMS Doctor
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

//Fetch user information
$checkuserid = !empty($userid) && $user = $DB->get_record('user', array('id' => $userid), 'id,password');
//allowing for fallback check of old url - MDL-27542
$checkusername = !empty($username) && $user = $DB->get_record('user', array('username' => $username), 'id,password');
if (!$checkuserid && !$checkusername) {
    //No such user
    die('Invalid authentication');
}

//Check authentication token
$authuserid = !empty($userid) && $authtoken == sha1($userid . $user->password . $CFG->calendar_exportsalt);
//allowing for fallback check of old url - MDL-27542
$authusername = !empty($username) && $authtoken == sha1($username . $user->password . $CFG->calendar_exportsalt);
if (!$authuserid && !$authusername) {
    die('Invalid authentication');
}

// Get the calendar type we are using.
$calendartype = \core_calendar\type_factory::get_calendar_instance();

$now = $calendartype->timestamp_to_date_array(time());

// Let's see if we have sufficient and correct data
$paramcategory = false;
$limitnum = 0;

$event = \calendar_event::load($eventid);

$ical = new iCalendar;
$ical->add_property('method', 'PUBLISH');
$ical->add_property('prodid', '-//Moodle Pty Ltd//NONSGML Moodle Version ' . $CFG->version . '//EN');

$hostaddress = str_replace('http://', '', $CFG->wwwroot);
$hostaddress = str_replace('https://', '', $hostaddress);

// $me = new \calendar_event($event); // To use moodle calendar event services.
$ev = new \iCalendar_event; // To export in ical format.
$ev->add_property('uid', $event->id.'@'.$hostaddress);

// Set iCal event summary from event name.
$ev->add_property('summary', format_string($event->name, true));

// Format the description text.
$description = format_text($event->description, $event->format);
// Then convert it to plain text, since it's the only format allowed for the event description property.
// We use html_to_text in order to convert <br> and <p> tags to new line characters for descriptions in HTML format.
$description = html_to_text($description, 0);
$ev->add_property('description', $description);

$ev->add_property('class', 'PUBLIC'); // PUBLIC / PRIVATE / CONFIDENTIAL
$ev->add_property('last-modified', Bennu::timestamp_to_datetime($event->timemodified));

if (!empty($event->location)) {
    $ev->add_property('location', $event->location);
}

$ev->add_property('dtstamp', Bennu::timestamp_to_datetime()); // now
if ($event->timeduration > 0) {
    //dtend is better than duration, because it works in Microsoft Outlook and works better in Korganizer
    $ev->add_property('dtstart', Bennu::timestamp_to_datetime($event->timestart)); // when event starts.
    $ev->add_property('dtend', Bennu::timestamp_to_datetime($event->timestart + $event->timeduration));
} else if ($event->timeduration == 0) {
    // When no duration is present, the event is instantaneous event, ex - Due date of a module.
    // Moodle doesn't support all day events yet. See MDL-56227.
    $ev->add_property('dtstart', Bennu::timestamp_to_datetime($event->timestart));
    $ev->add_property('dtend', Bennu::timestamp_to_datetime($event->timestart));
} else {
    // This can be used to represent all day events in future.
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
header('Accept-Ranges: none'); // Comment out if PDFs do not work...
header('Content-disposition: attachment; filename='.$filename);
header('Content-length: '.strlen($serialized));
header('Content-type: text/calendar; charset=utf-8');

echo $serialized;
