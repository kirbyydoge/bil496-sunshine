<?php
namespace local_coursetocal;

use moodle_url;
use html_writer;
use core_course_list_element;
use str_replace;
use stdClass;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/calendar/lib.php');


class helper {
    public static function create_event($event) {

        $config         = get_config('local_coursetocal');
        $configexport = (isset($config->exportcal)) ? $config->exportcal : get_string('exportcal', 'local_coursetocal');
        $courseinfo     = $event->get_data();
        $details        = $event->get_record_snapshot('course', $courseinfo['courseid']);
        $dateinfo       = self::get_course_dates($details->id);
        $summaryfile    = self::get_coursesummaryfile($details);

        $candocategory = self::validate_category($details->category);

        if (!$candocategory) {
            return;
        }

        $attr2 = array('class' => 'd-block col-3 mt-2 btn btn btn-primary');
        $courseurl = new moodle_url("/course/view.php?id=" . $courseinfo['courseid']);
        $linkurl = html_writer::link($courseurl, $config->title, $attr2);



        $data = self::build_data(
            $details->fullname,
            $details->id,
            $details->startdate,
            $details->enddate,
            $details->visible
        );

        $event = \calendar_event::create($data);
        $eventid = self::get_eventid($details->id);
        $params = array('eventid' => $eventid->id);
        $calurl = new moodle_url('/local/coursetocal/exportcal.php', $params);
        $attr = array('class' => 'd-block col-3 mt-2 btn btn btn-default');
        $linkurl .= html_writer::link($calurl, $configexport, $attr);
        $event->description = $details->summary . "<br>" . $summaryfile . $linkurl;

        $event->update($data);

    }

    protected static function build_data($fullname, $uuid, $tstart, $tend, $visible) {
        $data = new stdClass();

        $data->eventtype       = 'site';
        $data->type            = '-99';
        $data->name            = $fullname;
        $data->uuid            = $uuid;
        $data->courseid        = 1;
        $data->groupid         = 0;
        $data->userid          = 2;
        $data->modulename      = 0;
        $data->instance        = 0;
        $data->timestart       = $tstart;
        $data->visible         = (empty($visible)) ? 0 : 1;
        $data->timeduration    = $tend - $tstart;

        return $data;
    }


    protected static function build_datadue($fullname, $uuid, $tstart, $tend, $visible) {
        $data = new stdClass();

        $data->eventtype       = 'due';
        $data->type            = '1';
        $data->name            = $fullname;
        $data->uuid            = $uuid;
        $data->courseid        = 1;
        $data->groupid         = 0;
        $data->userid          = 2;
        $data->modulename      = 'assign';
        $data->instance        = 0;
        $data->timestart       = $tstart;
        $data->visible         = (empty($visible)) ? 0 : 1;
        $data->timeduration    = $tend - $tstart;

        return $data;
    }

    public static function get_coursesummaryfile($course) {
        global $CFG;

        $course = new core_course_list_element($course);
        $output = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            if ($file->is_valid_image()) {
                $imagepath = '/' . $file->get_contextid() .
                        '/' . $file->get_component() .
                        '/' . $file->get_filearea() .
                        $file->get_filepath() .
                        $file->get_filename();
                $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $imagepath,
                        false);
                $output = html_writer::tag('div',
                        html_writer::empty_tag('img', array('src' => $imageurl)),
                        array('class' => 'courseimage'));
                $output .= html_writer::empty_tag('br');
                $output .= html_writer::empty_tag('br');
                break;
            } else {
                $filepath = '/' . $file->get_contextid() .
                        '/' . $file->get_component() .
                        '/' . $file->get_filearea() .
                        $file->get_filepath() .
                        $file->get_filename();
                $fileurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $filepath, false);
                $output = html_writer::link($fileurl, $file->get_filename());
                $output .= html_writer::empty_tag('br');
                $output .= html_writer::empty_tag('br');
                break;
            }
        }
        return $output;

    }

    public static function update_event($event) {

        $config     = get_config('local_coursetocal');
        $configexport = (isset($config->exportcal)) ? $config->exportcal : get_string('exportcal', 'local_coursetocal');
        $courseinfo = $event->get_data();
        $details    = $event->get_record_snapshot('course', $courseinfo['courseid']);
        $summaryfile  = self::get_coursesummaryfile($details);

        $candocategory = self::validate_category($details->category);
        $eventid = self::get_eventid($courseinfo['courseid']);

        if (!$candocategory && !empty($eventid)) {
            $event = \calendar_event::load($eventid);
            $event->name = $courseinfo['other']['fullname'];
            $event->timestart = $details->startdate;
            $event->repeatid = 0;
            $event->delete();
        } else if (!$candocategory) {
            return;
        }

        $attr2 = array('class' => 'd-block col-3 mt-2 btn btn btn-primary');
        $courseurl  = new moodle_url("/course/view.php?id=" . $courseinfo['courseid']);
        $linkurl    = html_writer::link($courseurl, $config->title, $attr2);

        $params = array('eventid' => $eventid->id);
        $calurl = new moodle_url('/local/coursetocal/exportcal.php', $params);
        $attr = array('class' => 'd-block col-3 mt-2 btn btn btn-default');
        $linkurl .= html_writer::link($calurl, $configexport, $attr);

        $data = new stdClass;
        $data->name            = $details->fullname;
        $data->description     = $details->summary . "<br>" . $summaryfile . $linkurl;
        $data->timestart       = $details->startdate;
        $data->timeduration    = $details->enddate - $details->startdate;
        $data->type            = '-99';
        $data->eventtype       = 'site';
        $data->modulename      = 0;
        $data->visible         = (empty($details->visible)) ? 0 : 1;

        if (empty($eventid)) {
            self::create_event($event);
            $eventid = self::get_eventid($courseinfo['courseid']);
        }
        $candocategory = self::validate_category($details->category);
        if (!$candocategory) {
            return;
        }

        $event = \calendar_event::load($eventid);
        $event->update($data);

    }

    public static function delete_event($event) {

        $courseinfo = $event->get_data();
        $details    = $event->get_record_snapshot('course', $courseinfo['courseid']);

        $candocategory = self::validate_category($details->category);
        if (!$candocategory) {
            return;
        }

        $eventid = self::get_eventid($courseinfo['courseid']);
        $events = \calendar_event::load($eventid);
        $events->delete();

    }

    public static function sync_events() {
        global $CFG, $DB;
        $DB->delete_records('event', array('eventtype' => 'site', 'type' => '-99'));
        $config = get_config('local_coursetocal');
        if (empty($config->categories)) {
            $sql1 = "SELECT id,category,fullname,startdate,enddate,summary,visible FROM {course}";
        } else {
            $cats = preg_split('/,/', $config->categories);
            $sql1 = "SELECT id,category,fullname,startdate,enddate,summary,visible FROM {course}";

            $where = " WHERE ";
            foreach ($cats as $cat) {
                $where .= " category = $cat OR";
            }

            if ($cats) {
                $where = substr($where, 0, -2);
                $sql1 .= $where;
            }
        }

        $courses = $DB->get_records_sql($sql1);

        $cid            = $DB->get_field_sql("SELECT id FROM {course} WHERE category = ?", array(0));
        $configtitle    = (isset($config->title)) ? $config->title : get_string('gotocourse', 'local_coursetocal');
        $configexport    = (isset($config->exportcal)) ? $config->exportcal : get_string('exportcal', 'local_coursetocal');

        mtrace('Course to cal events will begin to sync.');
        echo '<br><br>';
        foreach ($courses as $course) {

            if ($course->id == 1) {
                continue;
            }

            $summaryfile  = self::get_coursesummaryfile($course);

            $tday = getdate();
            $data = new stdClass();
            $data->name         = $course->fullname;
            $data->format       = 1;
            $data->courseid     = 1;
            $data->uuid         = $course->id;
            $data->groupid      = 0;
            $data->userid       = 2;
            $data->repeatid     = 0;
            $data->modulename   = 0;
            $data->instance     = 0;
            $data->eventtype    = 'site';
            $data->type         = '-99';
            $data->timestart    = $course->startdate;
            $data->timeduration = $course->enddate - $course->startdate;
            $data->timemodified = $tday['0'];
            $data->sequence     = 1;
            $data->visible      = (empty($course->visible)) ? 0 : 1;

            $sql = 'SELECT id from {event} WHERE uuid = ? AND eventtype = ? AND type = ?';
            if ($DB->record_exists_sql($sql, array( $course->id, 'site', '-99' ))) {
                $data->id = $DB->get_field_sql($sql, array( $course->id, 'site', '-99') );
                $event = \calendar_event::load($data->id);

                $attr2 = array('class' => 'd-block col-3 mt-2 btn btn btn-primary');
                $courseurl  = new moodle_url("/course/view.php?id=" . $course->id);
                $linkurl    = html_writer::link($courseurl, $configtitle, $attr2);

                $params = array('eventid' => $event->id);
                $attr = array('class' => 'd-block col-3 mt-2 btn btn btn-default');
                $linkurl .= html_writer::link(
                    new moodle_url('/local/coursetocal/exportcal.php', $params),
                    $configexport, $attr
                );
                $data->description = $course->summary . "<br>" . $summaryfile . $linkurl;

                $event->update($data);
                mtrace('Events updated for the course ' . $course->fullname);
                echo '<br>';

            } else {
                $event = \calendar_event::create($data);
                $attr2 = array('class' => 'd-block col-3 mt-2 btn btn btn-primary');
                $courseurl  = new moodle_url("/course/view.php?id=" . $course->id);
                $linkurl    = html_writer::link($courseurl, $configtitle, $attr2);

                $params = array('eventid' => $event->id);
                $attr = array('class' => 'd-block col-3 mt-2 btn btn btn-default');
                $linkurl .= html_writer::link(
                    new moodle_url('/local/coursetocal/exportcal.php', $params),
                    $configexport, $attr
                );
                $data->description = $course->summary . "<br>" . $summaryfile . $linkurl;

                $event->update($data);

                mtrace('Events updated for the course ' . $course->fullname);
                echo '<br>';

            }

        }

        mtrace('Sync finished. You can close this window.');
        echo '<br><br>';

        return true;
    }

    public static function get_eventid($courseid) {
        global $DB;
        return $DB->get_record('event', array('uuid' => $courseid, 'courseid' => 1), 'id');
    }
    protected static function validate_category($coursecategory) {
        $config     = get_config('local_coursetocal');
        $categories = preg_split('/,/', $config->categories);

        $candocategory = false;
        foreach ($categories as $category) {
            if ($category == $coursecategory) {
                $candocategory = true;
            }
        }

        return $candocategory;

    }


    public static function get_course_dates($courseid) {
        global $DB;
        return $DB->get_record('course', array('id' => $courseid), 'id, summary, startdate, enddate');
    }


    public static function update_course($event) {
        global $DB;

        $config = get_config('local_coursetocal');
        $configtitle = (isset($config->title)) ? $config->title : get_string('gotocourse', 'local_coursetocal');

        $e          = $event->get_data();
        $details    = $event->get_record_snapshot('event', $e['objectid']);

        if ($details->type != '-99') {
            return;
        }

        $startdate  = $details->timestart;
        $enddate    = $details->timeduration + $startdate;

        $data               = new stdClass;
        $data->id           = $details->uuid;
        $data->fullname     = $details->name;
        $data->startdate    = $startdate;
        $data->enddate      = $enddate;

        $DB->update_record('course', $data);

    }

    public static function is_a_course($eventid) {
        global $DB;
        $conditions = array('id' => $eventid, 'eventtype' => 'ctc_site');
        return $DB->get_record('event', $conditions,  'id, eventtype, uuid');
    }

    public static function get_course_categories() {
        global $DB;
        $catlist = $DB->get_records('course_categories');
        $categories = [];
        foreach ($catlist as $r) {
            $categories[$r->id] = $r->name;
        }
        return $categories;
    }

}