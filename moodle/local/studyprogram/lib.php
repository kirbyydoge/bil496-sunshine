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

const EVENT_ID = 0;
const EVENT_NAME = 1;
const EVENT_TIME = 2;

function analyze_user_dates($user_events, $study_width) {
    $study_dates = array();
    for($i = count($user_events) - 1; $i > 0; $i--) {
        $cur_event = $user_events[$i];
        $prev_event = $user_events[$i-1];
        $cur_time = $cur_event[EVENT_TIME];
        $prev_time = $prev_event[EVENT_TIME];
        if($cur_time - $prev_time > $study_width) {
            $study_dates[$cur_event[EVENT_ID]] = [$cur_event[EVENT_NAME], $cur_time - $study_width];
        }
    }
    $fist_event = $user_events[0];
    $study_dates[$fist_event[EVENT_ID]] = [$fist_event[EVENT_NAME], $fist_event[EVENT_TIME] - $study_width];
    return $study_dates;
}