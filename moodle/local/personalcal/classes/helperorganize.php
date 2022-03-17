<?php

//Organize observer events.


namespace local_personalcal ;

use moodle_url;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/calendar/lib.php');

class helperorganize{

    public static function create_event($event){

        $config         = get_config('local_personalcal');
        $configexport = (isset($config->exportcal)) ? $config->exportcal : get_string('exportcal', 'local_personalcal');
        $courseinfo     = $event->get_data();
        $details        = $event->get_record_snapshot('course', $courseinfo['courseid']);
        $dateinfo       = self::get_course_dates($details->id);
        $summaryfile    = self::get_coursesummaryfile($details);


        $ablecategory = self::validate_category($details->category);

        if (!$ablecategory) {
            return;
        }

        $attrpl = array('class' => 'd-block col-3 mt-2 btn btn btn-primary');
        $courseurl = new moodle_url("/course/view.php?id=" . $courseinfo['courseid']);
        $linkurl = html_writer::link($courseurl, $config->title, $attrpl);

        $data = self::create_data(
            $details->fullname,
            $details->id,
            $details->startdate,
            $details->enddate,
            $details->visible
        );

        $event = \calendar_event::create($data);
        $eventid = self::get_eventid($details->id);
        $params = array('eventid' => $eventid->id);
        $calurl = new moodle_url('/local/mycalendar/exportcal.php', $params);
        $attrpl0 = array('class' => 'd-block col-3 mt-2 btn btn btn-default');
        $linkurl .= html_writer::link($calurl, $configexport, $attrpl0);
        $event->description = $details->summary . "<br>" . $summaryfile . $linkurl;

        $event->update($data);

    }

    protected static function create_data($fullname, $uuid, $tstart, $tend, $visible) {
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

}
