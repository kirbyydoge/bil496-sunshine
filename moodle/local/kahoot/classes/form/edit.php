<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class kahootedit extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 

        $mform->addElement('text', 'messagetext', get_string('email')); // Add elements to your form.
        $mform->setType('messagetext', PARAM_NOTAGS);                   // Set type of element.
        $mform->setDefault('messagetext', 'Please enter email');        // Default value.
        $choices=array();
        $choices['0']=\core\output\notification::NOTIFY_WARNING;
        $choices['1']=\core\output\notification::NOTIFY_SUCCESS;
        $choices['2']=\core\output\notification::NOTIFY_ERROR;
        $choices['3']=\core\output\notification::NOTIFY_INFO;
        
        $mform->addElement('select','messagetype',"Message Type",$choices);
        $mform->setDefault("messagetype","3");

        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

}