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

namespace core_question\output;

use moodle_url;
use renderer_base;
use templatable;
use renderable;
use url_select;

/**
 * Rendered HTML elements for tertiary nav for Question bank.
 *
 * @package   core_question
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @package core_question
 */
class qbank_actionbar implements templatable, renderable {
    /** @var moodle_url */
    private $currenturl;

    /**
     * qbank_actionbar constructor.
     *
     * @param moodle_url $currenturl The current URL.
     */
    public function __construct(moodle_url $currenturl) {
        $this->currenturl = $currenturl;
    }

    /**
     * Provides the data for the template.
     *
     * @param renderer_base $output renderer_base object.
     * @return array data for the template
     */
    public function export_for_template(renderer_base $output): array {
        $questionslink = new moodle_url('/question/edit.php', $this->currenturl->params());
        if (\core\plugininfo\qbank::is_plugin_enabled("qbank_managecategories")) {
            $categorylink = new moodle_url('/question/bank/managecategories/category.php', $this->currenturl->params());
        }
        $importlink = new moodle_url('/question/bank/importquestions/import.php', $this->currenturl->params());
        $exportlink = new moodle_url('/question/bank/exportquestions/export.php', $this->currenturl->params());

        $menu = [
                $questionslink->out(false) => get_string('questions', 'question'),
        ];

        if (\core\plugininfo\qbank::is_plugin_enabled("qbank_managecategories")) {
            $menu[$categorylink->out(false)] = get_string('categories', 'question');
        }
        $menu[$importlink->out(false)] = get_string('import', 'question');
        $menu[$exportlink->out(false)] = get_string('export', 'question');
        $additional = $this->get_additional_menu_elements();
        $menu += $additional ?: [];

        $urlselect = new url_select($menu, $this->currenturl->out(false), null, 'questionbankaction');
        $urlselect->set_label('questionbankactionselect', ['class' => 'accesshide']);

        return ['questionbankselect' => $urlselect->export_for_template($output)];
    }

    /**
     * Gets the additional third party navigation nodes.
     *
     * @return array|null The additional menu elements.
     */
    protected function get_additional_menu_elements(): ?array {
        global $PAGE;
        $qbnode = $PAGE->settingsnav->find('questionbank', \navigation_node::TYPE_CONTAINER);
        $othernodes = [];
        foreach ($qbnode->children as $key => $value) {
            if (array_search($value->key, $this->expected_nodes()) === false) {
                $othernodes[] = $value;
            }
        }
        $result = \core\navigation\views\secondary::create_menu_element($othernodes, true);
        return $result;
    }

    /**
     * Returns a list of expected child navigation nodes for 'questionbank'.
     *
     * @return array The expected nodes
     */
    protected function expected_nodes(): array {
        return ['questions', 'categories', 'import', 'export'];
    }
}
