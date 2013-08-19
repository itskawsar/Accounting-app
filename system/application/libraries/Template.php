<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// template library. view er jonno data gulo collect kora & layout onujaye data gulo ke provide kora ei class er kaj
class Template {
	// templage data container. ei array te vibinno controller, model, helper theke pathano data set kora hoi
	var $template_data = array();
	
	// view er jonno data gulo template data ARRAY container a store kora hoi. ei mathod a 2ti PARAM dite hobe. PARAM gulo holo KEY and VALUE
	function set($name, $value) {
		// template data te KEY & VALUE set kora holo
		$this->template_data[$name] = $value;
	}

	// template data ARRAY container ti te store kora data gulo ke process kora
	// & view er jonno VIEW FILE(app_root/application/views er file gulo) selecte
	// kora ei method tir kaj. ei method a 4 ti PARAM dite hobe.
	// 1st (template) ti VIEW FILE er naam,
	// 2nd ti main content window er jonno VIEW FILE,
	// 3rd ti holo main content er data
	// 4th ti holo template ti sorasori ECHO/PRINT korbe kina
	// RETURN type MIXED
	function load($template = '', $view = '' , $view_data = array(), $return = FALSE) {
		// CodeIgniter er built-in feature gulo ei method a use korte declare kora holo
		$this->CI =& get_instance();

		// main content part ti set kora holo
		$this->set('contents', $this->CI->load->view($view, $view_data, TRUE));
		// template ti generate korar por return korbe
		return $this->CI->load->view($template, $this->template_data, $return);
	}
}

/* End of file Template.php */
/* Location: ./system/application/libraries/Template.php */