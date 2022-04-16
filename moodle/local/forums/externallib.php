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
 * local_forums external file
 *
 * @package    component
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/local/forums/classes/manager.php');
require_once($CFG->libdir . "/externallib.php");

class local_forums_external extends external_api  {
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function delete_forums_parameters() {
        return new external_function_parameters(
            ['messageid' => new external_value(PARAM_INT, 'id of forums')],
        );
    }

    /**
     * The function itself
     * @param $id
     * @return string
     */
    public static function delete_forums($id): string {

        $params = self::validate_parameters(self::delete_forums_parameters(), array('messageid'=>$id));
        $manager = new manager();
        return $manager->delete_forums($id);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function delete_forums_returns() {
        return new external_value(PARAM_BOOL, 'True if the message was successfully deleted.');
    }
}

