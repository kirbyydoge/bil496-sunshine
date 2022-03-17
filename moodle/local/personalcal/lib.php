<?php


function local_message_before_footer()
{

    \core\notification::add('this is a test message for local calendar', \core\output\notification::NOTIFY_SUCCESS);


}