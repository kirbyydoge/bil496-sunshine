<?php


defined('MOODLE_INTERNAL') || die();

$observers = array(
    array (
        'eventname'   => '\core\event\course_updated',
        'callback'    => '\local_coursetocal\helper::update_event',
    ),

    array (
        'eventname'   => '\core\event\course_created',
        'callback'    => '\local_coursetocal\helper::create_event',
    ),

    array (
        'eventname'   => '\core\event\course_restored',
        'callback'    => '\local_coursetocal\helper::create_event',
    ),

    array (
        'eventname'   => '\core\event\course_deleted',
        'callback'    => '\local_coursetocal\helper::delete_event',
    ),

    array (
        'eventname'   => '\core\event\course_category_updated',
        'callback'    => '\local_coursetocal\helper::sync_events',
    ),

    array (
        'eventname'   => '\core\event\calendar_event_updated',
        'callback'    => '\local_coursetocal\helper::update_course',
    ),
);
