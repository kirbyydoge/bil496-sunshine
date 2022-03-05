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
 * Settings
 *
 * @package    local_coursetocal
 * @copyright  2020 LMS DOCTOR
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $settings = new admin_settingpage( 'local_coursetocal', get_string('pluginname', 'local_coursetocal') );
    $categories = \local_coursetocal\helper::get_course_categories();

    if ($ADMIN->fulltree) {

        $settings->add(
            new admin_setting_configmultiselect(
                'local_coursetocal/categories',
                get_string('categoriestoshow', 'local_coursetocal'),
                get_string('categoriestoshow_desc', 'local_coursetocal'),
                array(1),
                $categories
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'local_coursetocal/title',
                get_string('linktitle', 'local_coursetocal'),
                get_string('linktitle_desc', 'local_coursetocal'),
                get_string('gotocourse', 'local_coursetocal')
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'local_coursetocal/exportcal',
                get_string('exporttitle', 'local_coursetocal'),
                get_string('exporttitle_desc', 'local_coursetocal'),
                get_string('exportcal', 'local_coursetocal')
            )
        );

        $syncurl = new moodle_url('/local/coursetocal/sync.php');

        // Button to manually update the plugin.
        $settings->add(
            new admin_setting_configempty(
                'local_coursetocal/syncbutton',
                get_string('syncevents', 'local_coursetocal'),
                '<a target="_blank" href="' . $syncurl . '" class="btn btn-primary">' . get_string('syncevents', 'local_coursetocal') . '</a> ' . get_string('syncevents_desc', 'local_coursetocal')
            )
        );

    }
    $ADMIN->add('localplugins', $settings);

}
