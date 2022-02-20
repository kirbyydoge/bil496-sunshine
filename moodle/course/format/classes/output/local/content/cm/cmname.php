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
 * Contains the default activity name inplace editable.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\cm;

use cm_info;
use core_courseformat\base as course_format;
use section_info;
use stdClass;
use context_module;
use lang_string;
use external_api;
use core\output\inplace_editable;

/**
 * Base class to render a course module inplace editable header.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cmname extends inplace_editable {

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section object */
    private $section;

    /** @var cm_info the course module instance */
    protected $mod;

    /** @var editable if the title is editable */
    protected $editable;

    /** @var array optional display options */
    protected $displayoptions;

    /** @var string the activity title output class name */
    protected $titleclass;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the course module ionfo
     * @param bool $editable if it is editable
     * @param array $displayoptions optional extra display options
     */
    public function __construct(
        course_format $format,
        section_info $section,
        cm_info $mod,
        bool $editable,
        array $displayoptions = []
    ) {
        $this->format = $format;
        $this->section = $section;
        $this->mod = $mod;
        $this->displayoptions = $displayoptions;

        $this->editable = $editable && has_capability(
            'moodle/course:manageactivities',
            $mod->context
        );

        // Get the necessary classes.
        $this->titleclass = $format->get_output_classname('content\\cm\\title');

        // Setup inplace editable.
        parent::__construct(
            'core_course',
            'activityname',
            $mod->id,
            $this->editable,
            $mod->name,
            $mod->name,
            new lang_string('edittitle'),
            new lang_string('newactivityname', '', $mod->get_formatted_name())
        );
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): array {
        global $PAGE;

        // Inplace editable uses core renderer by default. However, course elements require
        // the format specific renderer.
        $courseoutput = $this->format->get_renderer($PAGE);

        // Inplace editable uses pre-rendered elements and does not allow line beaks in the UI value.
        $title = new $this->titleclass(
            $this->format,
            $this->section,
            $this->mod,
            $this->displayoptions
        );
        $this->displayvalue = str_replace("\n", "", $courseoutput->render($title));

        if (trim($this->displayvalue) == '') {
            $this->editable = false;
        }
        $data = parent::export_for_template($output);

        return $data;
    }

    /**
     * Updates course module name
     *
     * @param int $itemid course module id
     * @param string $newvalue new name
     * @return static
     */
    public static function update($itemid, $newvalue) {
        $context = context_module::instance($itemid);
        // Check access.
        external_api::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);

        // Trim module name and Update value.
        set_coursemodule_name($itemid, trim($newvalue));
        $coursemodulerecord = get_coursemodule_from_id('', $itemid, 0, false, MUST_EXIST);
        // Return instance.
        $modinfo = get_fast_modinfo($coursemodulerecord->course);
        $cm = $modinfo->get_cm($itemid);
        $section = $modinfo->get_section_info($cm->sectionnum);

        $format = course_get_format($cm->course);
        return new static($format, $section, $cm, true);
    }
}
