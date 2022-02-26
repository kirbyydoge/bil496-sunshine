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

const EVENT_ID = "id";
const EVENT_COURSEID = "courseid";
const EVENT_NAME = "name";
const EVENT_TIME = "timestart";
const EVENT_STUDY_START = "start_date";
const SECONDS_PER_DAY = 86400;

const STUDY_ADVICE_TYPE = "STUDY_ADVICE";

class Cardinal {

    public function analyze_user_dates_simple($user_events, $study_width) {
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

    public function handle_collisions(& $study_dates, & $colliding_stack, $cur_time, $prev_time, $loosen = 3600) {
        $prev_time += $loosen; // Loosens some time after a deadline.
        while((!$colliding_stack->isEmpty()) && ($cur_time - $prev_time > 0)) {
            $top_event = $colliding_stack->pop();
            if($cur_time - $prev_time > $top_event["study_width"]) {
                $duration = $top_event["study_width"];
            }
            else {
                $duration = $cur_time - $prev_time;
                $top_event["study_width"] -= $duration;
                $colliding_stack->push($top_event);
            }
            $cur_time -= $duration;
            $study_dates[] = [
                "id" => $top_event[EVENT_ID],
                "courseid" => $top_event[EVENT_COURSEID],
                "name" => $top_event[EVENT_NAME],
                "start_date" => $cur_time,
                "duration" => $duration
            ];
        }
    }

    public function analyze_user_dates_advanced($user_events, $study_width) {
        $study_dates = array();
        $colliding_stack = new Stack(count($user_events));
        for($i = 0; $i < count($user_events); $i++) {
            if(!array_key_exists("study_width", $user_events[$i])) {
                $user_events[$i]["study_width"] = $study_width;
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
        $this->handle_collisions($study_dates, $colliding_stack, $user_events[0][EVENT_TIME], 0);
        return $study_dates;
    }

    public function create_studyprogram_event($userid, $studyevent) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/calendar/lib.php');

        $event = new stdClass();
        $event->eventtype = STUDY_ADVICE_TYPE; // Constant defined somewhere in your code - this can be any string value you want. It is a way to identify the event.
        $event->type = CALENDAR_EVENT_TYPE_STANDARD; // This is used for events we only want to display on the calendar, and are not needed on the block_myoverview.
        $event->name = $studyevent[EVENT_NAME];
        $event->description = get_string("start_study", "local_studyprogram");
        $event->format = FORMAT_HTML;
        $event->courseid = $studyevent[EVENT_COURSEID];
        $event->groupid = 0;
        $event->userid = 0;
        $event->modulename = 'local_studyprogram';
        $event->instance = 0;
        $event->timestart = $studyevent[EVENT_STUDY_START];
        $event->visible = 1;
        $event->timeduration = 0;

        $db_entry = calendar_event::create($event);
        $this->create_studyprogram_record($userid, $db_entry->id);
    }

    public function create_studyprogram_record($userid, $eventid) {
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

    public function delete_studyprogram_record($recordid) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $deleted_record = $DB->delete_records("local_studyprogram_events", ["id" => $recordid]);
        if($deleted_record) {
            $DB->commit_delegated_transaction($transaction);
        }
        return true;
    }
}