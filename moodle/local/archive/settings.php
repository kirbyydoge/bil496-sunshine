<<?php
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
 * @package    local_archive
 * @author     Elcin Duman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $ADMIN;
if ($hassiteconfig) { // needs this condition or there is error on login page

    $ADMIN->add('localplugins', new admin_category('local_archive_category', get_string('pluginname', 'local_archive')));

    $settings = new admin_settingpage('local_archive', get_string('pluginname', 'local_archive'));
    $ADMIN->add('local_archive_category', $settings);

    $settings->add(new admin_setting_configcheckbox('local_archive/enabled',
        get_string('setting_enable', 'local_archive'), get_string('setting_enable_desc', 'local_archive'), '1'));

    $ADMIN->add('local_archive_category', new admin_externalpage('local_archive_manage', get_string('manage', 'local_archive'),
        $CFG->wwwroot . '/local/archive/manage.php'));
}