<?php

class block_examsobs extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_examsobs');
    }


    function get_content() {

        if ($this->content !== NULL) {
            return $this->content;
        }

        $content = 'OBS activation are shown...';
        $this->content = new stdClass;
        $this->content->text = $content;
        $this->content->footer = 'Click here to reach obs';
        return $this->content;
    }
}
