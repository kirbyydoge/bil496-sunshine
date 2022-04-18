<?php

class block_examsobs extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_examsobs');
    }


    function get_content() {
        $id          = optional_param('id', 0, PARAM_INT);
        // echo "<script type='text/javascript'>alert('$id');</script>";
        if ($this->content !== NULL) {
            return $this->content;
        }

        $content = 'OBS activation are shown...';
        $this->content = new stdClass;
        $this->content->text = $content;
        
        $url=new moodle_url('/course/view.php', array('id' => $id, 'flag' => 3));
        $url2=new moodle_url('/local/obsexamsreports/examsreport.php', array('id' => $id));
        $this->content->text = html_writer::div(
            html_writer::link($url2, "Click here to reach exam reports"),
        );
        $this->content->footer = html_writer::div(
            
            html_writer::link($url, "Click here to start OBS"),
        );
      
        return $this->content;
    }
}
