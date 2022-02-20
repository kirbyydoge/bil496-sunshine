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

declare(strict_types=1);

namespace core_reportbuilder\form;

use context;
use context_system;
use core_form\dynamic_form;
use core_reportbuilder\local\audiences\base;
use core_reportbuilder\manager;
use core_reportbuilder\permission;
use core_reportbuilder\report_access_exception;
use moodle_exception;
use moodle_url;
use stdClass;

/**
 * Dynamic audience form
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class audience extends dynamic_form {
    /**
     * Audience we work with
     *
     * @return base
     */
    protected function get_audience(): base {
        $id = $this->optional_param('id', 0, PARAM_INT);

        $record = new stdClass();
        if (!$id) {
            // New instance, pre-define report id and classname.
            $record->reportid = $this->optional_param('reportid', null, PARAM_INT);
            $record->classname = $this->optional_param('classname', null, PARAM_RAW_TRIMMED);
        }
        return base::instance($id, $record);
    }

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'reportid');
        $mform->setType('reportid', PARAM_INT);

        $mform->addElement('hidden', 'classname');
        $mform->setType('classname', PARAM_RAW_TRIMMED);

        // Embed form defined in audience class.
        $audience = $this->get_audience();
        $audience->get_config_form($mform);

        $this->add_action_buttons();
    }

    /**
     * Form validation.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $audience = $this->get_audience();
        return $audience->validate_config_form($data);
    }

    /**
     * Returns context where this form is used
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return context_system::instance();
    }

    /**
     * Checks if current user has access to this form, otherwise throws exception
     *
     * @throws moodle_exception
     */
    protected function check_access_for_dynamic_submission(): void {
        $audience = $this->get_audience();

        // Check whether we are able to add/edit the current audience.
        $audience->get_persistent()->get('id') === 0
            ? $audience->require_user_can_add()
            : $audience->require_user_can_edit();
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     */
    public function process_dynamic_submission() {
        $formdata = $this->get_data();
        $audience = $this->get_audience();

        // Check permissions.
        $report = manager::get_report_from_id($formdata->reportid);
        permission::require_can_edit_report($report->get_report_persistent());

        $configdata = $audience::retrieve_configdata($formdata);
        if (!$formdata->id) {
            // New audience.
            $audience = $audience::create($formdata->reportid, $configdata);
        } else {
            // Editing audience.
            $audience->update_configdata($configdata, true);
        }
        return ['instanceid' => $audience->get_persistent()->get('id'), 'description' => $audience->get_description()];
    }

    /**
     * Load in existing data as form defaults
     */
    public function set_data_for_dynamic_submission(): void {
        $audience = $this->get_audience();
        if ($audience->get_persistent()->get('id') !== 0) {
            // Populate form with exisiting data.
            $formdata = [
                'id' => $audience->get_persistent()->get('id'),
                'reportid' => $audience->get_persistent()->get('reportid'),
            ];
            $formdata += $audience->get_configdata();
        } else {
            $formdata['reportid'] = $this->optional_param('reportid', null, PARAM_INT);
        }
        $formdata['classname'] = $this->optional_param('classname', null, PARAM_RAW_TRIMMED);
        $this->set_data($formdata);
    }

    /**
     * Page url
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/reportbuilder/edit.php', ['id' => $this->optional_param('reportid', 0, PARAM_INT)]);
    }
}
