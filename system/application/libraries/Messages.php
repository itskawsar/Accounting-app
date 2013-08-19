<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Message:: a class for writing feedback message information to the session
 *
 * Copyright 2006 Vijay Mahrra & Sheikh Ahmed <webmaster@designbyfail.com>
 *
 * See the enclosed file COPYING for license information (LGPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Vijay Mahrra & Sheikh Ahmed <webmaster@designbyfail.com>
 * @url http://www.designbyfail.com/
 * @version 1.0
 */

class Messages
{
    var $_ci;
    // message gular type nirdharon kora hobe
    var $_types = array('success', 'error', 'warning', 'message');

    function Messages($params = array())
    {
        $this->_ci =& get_instance();
        // session library ti load kora holo
        $this->_ci->load->library('session');
        // check if theres already messages, if not, initialise the messages array in the session
        // jodi session a kono message na thake tahole message ti clear kore felbe
        $messages = $this->_ci->session->userdata('messages');
        if (empty($messages)) {
            $this->clear();
        }
    }

    // clear all messages
    function clear()
    {
        $messages = array();
        // message ti type onujayee blank kore deya hocce
        foreach ($this->_types as $type) {
            $messages[$type] = array();
        }
        // blank array ti session er messages key ti replace kore deya holo
        $this->_ci->session->set_userdata('messages', $messages);
    }

    // add a message, default type is message
    function add($message, $type = 'message')
    {
        // message er lenge 1 er ceye choto hole return korbe
    	if (strlen($message) < 1)
    		return;
        // session theke message ti collect kora holo
        $messages = $this->_ci->session->userdata('messages');
        // handle PEAR errors gracefully
        // message ti jodi PEAR error er hoi
        // tahole message ti error type a set korar jonno prepare kore
        if (is_a($message, 'PEAR_Error')) {
            $message = $message->getMessage();
            $type = 'error';

        // message ti jodi PEAR error er na hoi
        // type ti class er property _types er kono ekti jodi na hoi/unknown hoi,
        // tahole default type "message" set kore
        } else if (!in_array($type, $this->_types)) {
            // set the type to message if the user specified a type that's unknown
            $type = 'message';
        }
        // don't repeat messages!
        // message ti repeat agee set kora hoyechilo kina?
        // jodi set kora na thake tahole notun kore set kora hobe
        if (!in_array($message, $messages[$type]) && is_string($message)) {
            $messages[$type][] = $message;
        }
        // message guli session er "messages" key te set kora holo
        $messages = $this->_ci->session->set_userdata('messages', $messages);
    }

    // return messages of given type or all types, return false if none
    function sum($type = null)
    {
        // session theke message ti collect kora holo
        $messages = $this->_ci->session->userdata('messages');
        // type empty na hole messages er oi type a koto guli message ace tar number ta return korbe
        if (!empty($type)) {
            $i = count($messages[$type]);
            return $i;
        }

        $i = 0;
        // type empty hole class er _types property er value ke extract kora hocce
        foreach ($this->_types as $type) {
            // sokol typer message er jog ber kora hocce
            $i += count($messages[$type]);
        }
        // jogfol ti return kora holo
        return $i;
    }

    // return messages of given type or all types, return false if none, clearing stack
    function get($type = null)
    {
        // session theke message ti collect kora holo
        $messages = $this->_ci->session->userdata('messages');
        // type empty na hole messages er oi type er message guli return korbe
        if (!empty($type)) {
            // message na thakle FALSE return korbe
            if (count($messages[$type]) == 0) {
                return false;
            }
            return $messages[$type];
        }
        // return false if there actually are no messages in the session
        $i = 0;

        // type empty hole class er _types property er value ke extract kora hocce
        foreach ($this->_types as $type) {
            // sokol typer message er jog ber kora hocce
            $i += count($messages[$type]);
        }
        // jogfol jodi 0 hoi,
        // tahole FALSE return korbe
        if ($i == 0) {
            return false;
        }

        // order return by order of type array above
        // i.e. success, error, warning and then informational messages last
        // type empty hole class er _types property er value ke extract kora hocce
        foreach ($this->_types as $type) {
            // class property _types er order onujayee message gula sajano hocce
            $return[$type] = $messages[$type];
        }
        // session theke message gula muche fela hocce
        $this->clear();
        // sajano message gula return kora hocce
        return $return;
    }
}  
