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
 * Define install function
 * @package    ltool_focus
 * @copyright  bdecent GmbH 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * ltool_focus install function.
 *
 * @return void
 */
function xmldb_ltool_focus_install() {
    global $CFG;
    require_once($CFG->dirroot. '/local/learningtools/lib.php');
    require_once($CFG->dirroot. '/local/learningtools/ltool/focus/lib.php');
    $plugin = 'focus';
    ltool_focus_create_focus_temp_cssfile();
    local_learningtools_add_learningtools_plugin($plugin);
}
