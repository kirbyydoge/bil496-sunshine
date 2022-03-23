<?php
// This file is part of Moodle Study Program Plugin
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
 * Version details
 *
 * @package    local_studyprogram
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include "Stack.php";
require_once(__DIR__ . "/../constants.php");

class Cardinal {

    public function analyze_user_dates_simple(array $user_events, int $study_width) {
        $study_dates = array();
        for($i = count($user_events) - 1; $i > 0; $i--) {
            $cur_event = $user_events[$i];
            $prev_event = $user_events[$i-1];
            $cur_time = $cur_event[EVENT_TIME];
            $prev_time = $prev_event[EVENT_TIME];
            if($cur_time - $prev_time > $study_width) {
                $study_dates[] = [$cur_event[EVENT_ID], $cur_event[EVENT_NAME], $cur_time - $study_width];
            }
        }
        $fist_event = $user_events[0];
        $study_dates[] = [$fist_event[EVENT_ID], $fist_event[EVENT_NAME], $fist_event[EVENT_TIME] - $study_width];
        return $study_dates;
    }

    public function format_event_name(string $event_name) {
        $formatted = str_replace("is due", "", $event_name);
        return get_string("start_study_format", "local_studyprogram", ["name" => $formatted]);
    }

    public function handle_collisions(array& $study_dates, Stack& $colliding_stack, int $cur_time, int $prev_time, int $loosen = 3600) {
        $prev_time += $loosen; // Loosens some time after a deadline.
        while((!$colliding_stack->isEmpty()) && ($cur_time - $prev_time > 0)) {
            $top_event = $colliding_stack->pop();
            if($cur_time - $prev_time > $top_event[EVENT_STUDY_WIDTH]) {
                $duration = $top_event[EVENT_STUDY_WIDTH];
            }
            else {
                $duration = $cur_time - $prev_time;
                $top_event[EVENT_STUDY_WIDTH] -= $duration;
                $colliding_stack->push($top_event);
            }
            $cur_time -= $duration;
            $study_dates[] = [
                EVENT_ID => $top_event[EVENT_ID],
                EVENT_COURSEID => $top_event[EVENT_COURSEID],
                EVENT_NAME => $this->format_event_name($top_event[EVENT_NAME]),
                EVENT_STUDY_START => $cur_time + 60,
                EVENT_DURATION => $duration
            ];
        }
    }

    public function analyze_user_dates_advanced(array $user_events, int $study_width) {
        $study_dates = array();
        $colliding_stack = new Stack(count($user_events));
        for($i = 0; $i < count($user_events); $i++) {
            if(!array_key_exists(EVENT_STUDY_WIDTH, $user_events[$i])) {
                $user_events[$i][EVENT_STUDY_WIDTH] = $study_width;
            }
        }
        for($i = count($user_events)-1; $i > 0; $i--) {
            $cur_event = $user_events[$i];
            $prev_event = $user_events[$i-1];
            $cur_time = $cur_event[EVENT_TIME];
            $prev_time = $prev_event[EVENT_TIME];
            $colliding_stack->push($cur_event);
            $this->handle_collisions($study_dates, $colliding_stack, $cur_time, $prev_time);
        }
        $colliding_stack->push($user_events[0]);
        $this->handle_collisions($study_dates, $colliding_stack, $user_events[0][EVENT_TIME], time());
        return $study_dates;
    }

    public function create_studyprogram(int $userid, array $study_program) {
        foreach($study_program as $entry) {
            $eventid = $this->create_studyprogram_event($userid, $entry);
            $this->create_studyprogram_record($userid, $eventid);
        }
    }

    public function create_studyprogram_event(int $userid, array $studyevent) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/calendar/lib.php');

        $event = new stdClass();
        $event->eventtype = STUDY_ADVICE_TYPE; // Constant defined somewhere in your code - this can be any string value you want. It is a way to identify the event.
        $event->type = CALENDAR_EVENT_TYPE_ACTION; // This is used for events we only want to display on the calendar, and are not needed on the block_myoverview.
        $event->name = $studyevent[EVENT_NAME];
        $event->description = get_string("start_study", "local_studyprogram");
        $event->format = FORMAT_HTML;
        $event->courseid = $studyevent[EVENT_COURSEID];
        $event->groupid = 0;
        $event->userid = 0;
        $event->modulename = 0;
        //$event->modulename = "local_studyprogram"; // Setting modulename makes these events invisible for some reason.
        $event->instance = 3;
        $event->timestart = $studyevent[EVENT_STUDY_START];
        $event->timesort = $studyevent[EVENT_STUDY_START];
        $event->visible = true;
        $event->timeduration = 0;

        $db_entry = calendar_event::create($event);
        return $db_entry->id;
    }

    public function create_studyprogram_record(int $userid, int $eventid) {
        global $DB;
        $study_event = new stdClass();
        $study_event->userid = $userid;
        $study_event->eventid = $eventid;
        try {
            return $DB->insert_record('local_studyprogram_events', $study_event, true);
        } catch (dml_exception $e) {
            return false;
        }
    }

    public function cleanup_studyprogram(int $userid) {
        global $DB;
        $user_records = $DB->get_records("local_studyprogram_events", ["userid" => $userid]);
        $transaction = $DB->start_delegated_transaction();
        $deletedrecords = $DB->delete_records("local_studyprogram_events", ["userid" => $userid]);
        if ($deletedrecords) {
            foreach($user_records as $record) {
                try {
                    $event = calendar_event::load($record->eventid);
                    $event->delete(true);
                }
                catch(dml_missing_record_exception $e) {
                    // Another entity (user/admin/another plugin) has removed this event. No longer valid.
                    continue;
                }
            }
            $DB->commit_delegated_transaction($transaction);
        }
        return true;
    }

    public function delete_studyprogram_record(int $recordid) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $deleted_record = $DB->delete_records("local_studyprogram_events", ["id" => $recordid]);
        if($deleted_record) {
            $DB->commit_delegated_transaction($transaction);
        }
        return true;
    }

    public function get_user_events_sorted(int $id) {
        //Get timestamp of current second and the second 30 days after.
        $time_offset = 30 * SECONDS_PER_DAY;
        $start_time = time();
        $end_time = $start_time + $time_offset;

        //Get user's enrolled courses.
        $enrolled_courses = enrol_get_all_users_courses($id);
        $user_events = array();

        //For each course, get due dates from calendar API
        foreach ($enrolled_courses as $course) {
            $course_events = calendar_get_events($start_time, $end_time, false, false, $course->id);
            $user_events[$course->id] = array();
            foreach ($course_events as $event) {
                if ($event->eventtype == "due") {
                    $user_events[] = [
                        EVENT_ID => $event->id,
                        EVENT_COURSEID => $course->id,
                        EVENT_NAME => $event->name,
                        EVENT_TIME => $event->timestart
                    ];
                }
            }
        }

        //Remove null elements.
        $user_events = array_filter($user_events, function ($val, $key) {
            return array_key_exists(EVENT_TIME, $val);
        }, ARRAY_FILTER_USE_BOTH);

        //Sort due dates from earliest to latest.
        usort($user_events, function ($a, $b) {
            $a_time = $a[EVENT_TIME];
            $b_time = $b[EVENT_TIME];
            return ($a_time < $b_time) ? -1 : (($a_time > $b_time) ? 1 : 0);
        });

        return $user_events;
    }
}