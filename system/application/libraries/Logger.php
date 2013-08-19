<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logger
{
	function Logger()
	{
		return;
	}

	/*
	 * Write message to database log
	 * Levels defined are :
	 * 0 - error
	 * 1 - success
	 * 2 - info
	 * 3 - debug
	 */
	// log message write korbe database
	function write_message($level = "debug", $title = "", $desc = "")
	{
		$CI =& get_instance();

		/* Check if logging is enabled. Skip if it is not enabled */
		// LOG enable ace kina check kore
		if ($CI->config->item('log') != "1")
			// na thakle return back
			return;

		// ajker date & time set kore
		$data['date'] = date("Y-m-d H:i:s");
		// default lavel 3 set kore
		$data['level'] = 3;
		// lavel er value test kore data['lavel'] er depth set kore
		switch ($level)
		{
			// lavel 0 hole
			case "error": $data['level'] = 0; break;
			// lavel 1 hole
			case "success": $data['level'] = 1; break;
			// lavel 2 hole
			case "info": $data['level'] = 2; break;
			// lavel 3 hole
			case "debug": $data['level'] = 3; break;
			// lavel 0 to 3 na hole
			default: $data['level'] = 0; break;
		}
		// browser theke user er ip address collect kore
		$data['host_ip'] = $CI->input->ip_address();

		// session theke user er user_name collect kore
		$data['user'] = $CI->session->userdata('user_name');

		// current URL ti collect kore
		$data['url'] = uri_string();

		// browser theke browser er information collect kore
		$data['user_agent'] = $CI->input->user_agent();

		// message_title & message_desc set kore
		$data['message_title'] = $title;
		$data['message_desc'] = $desc;
		// database er LOGS table a data array r value guli input kore
		$CI->db->insert('logs', $data);
		// return back
		return;
	}

	// recent message guli collect kore return kore
	function read_recent_messages()
	{
		$CI =& get_instance();
		// "SELECT * FROM logs ORDER BY id DESC LIMIT 20" statement ti generate kore
		$CI->db->from('logs')->order_by('id', 'desc')->limit(20);
		// uporer query ti run kore
		$logs_q = $CI->db->get();
		// query er number of row blank kina check korce
		if ($logs_q->num_rows() > 0)
		{
			// blank na hole data guli return korbe
			return $logs_q;
		} else {
			// blank hole FALSE return korbe
			return FALSE;
		}
	}
}

