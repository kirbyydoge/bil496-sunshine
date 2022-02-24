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
 * Version details
 *
 * @package    local_studyprogram
 * @author     Oğuzhan Canpolat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Stack {

    protected $stack;
    protected $limit;

    public function __construct($limit = 10, $initial = array()) {
        // initialize the stack
        $this->stack = $initial;
        // stack can only contain this many items
        $this->limit = $limit;
    }

    public function push($item) {
        // trap for stack overflow
        if (count($this->stack) < $this->limit) {
            // prepend item to the start of the array
            array_unshift($this->stack, $item);
        } else {
            throw new RunTimeException('Stack is full!');
        }
    }

    public function pop() {
        if ($this->isEmpty()) {
            // trap for stack underflow
            throw new RunTimeException('Stack is empty!');
        } else {
            // pop item from the start of the array
            return array_shift($this->stack);
        }
    }

    public function top() {
        return current($this->stack);
    }

    public function isEmpty() {
        return empty($this->stack);
    }
}

const EVENT_ID = 0;
const EVENT_NAME = 1;
const EVENT_TIME = 2;
const SECONDS_PER_DAY = 86400;

function analyze_user_dates_simple($user_events, $study_width) {
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

function handle_collisions(& $study_dates, & $colliding_stack, $cur_time, $prev_time, $loosen = 3600) {
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
            "name" => $top_event[EVENT_NAME],
            "start_date" => $cur_time,
            "duration" => $duration
        ];
    }
}

function analyze_user_dates_advanced($user_events, $study_width) {
    $study_dates = array();
    $colliding_stack = new Stack(count($user_events));
    for($i = 0; $i < count($user_events); $i++) {
        if(is_null($user_events[$i]["study_width"])) {
            $user_events[$i]["study_width"] = $study_width;
        }
    }
    for($i = count($user_events)-1; $i > 0; $i--) {
        $cur_event = $user_events[$i];
        $prev_event = $user_events[$i-1];
        $cur_time = $cur_event[EVENT_TIME];
        $prev_time = $prev_event[EVENT_TIME];
        $colliding_stack->push($cur_event);
        handle_collisions($study_dates, $colliding_stack, $cur_time, $prev_time);
    }
    $colliding_stack->push($user_events[0]);
    handle_collisions($study_dates, $colliding_stack, $user_events[0][EVENT_TIME], 0);
    return $study_dates;
}