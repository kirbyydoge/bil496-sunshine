<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class kahooteditbutton extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 

        $mform->addElement('button', 'intro', "anaButton");
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

}