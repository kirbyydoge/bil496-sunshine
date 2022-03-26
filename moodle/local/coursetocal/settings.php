<?php
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
