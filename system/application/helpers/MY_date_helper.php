<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('date_php_to_mysql'))
{
	// normal date theke mysql a store korar jonno mysql er date format a convert korbe
	function date_php_to_mysql($dt)
	{
		$CI =& get_instance();
		// current_date_format ti config theke current 'account_date_format' data ti nibe
		$current_date_format = $CI->config->item('account_date_format');
		// default date, month, year set kora holo
		list($d, $m, $y) = array(0, 0, 0);

		// 'current_date_format' ti check korbe
		// 'current_date_format' onujayee date, month, year set korbe
		// 'current_date_format' er format chara onno format hole error message dekhabe
		switch ($current_date_format)
		{
		case 'dd/mm/yyyy':
			list($d, $m, $y) = explode('/', $dt);
			break;
		case 'mm/dd/yyyy':
			list($m, $d, $y) = explode('/', $dt);
			break;
		case 'yyyy/mm/dd':
			list($y, $m, $d) = explode('/', $dt);
			break;
		default:
			$CI->messages->add('Invalid date format. Check your account settings.', 'error');
			return "";
		}
		// mysql format a date ti prepare kore return kora hobe
		$ts = mktime(0, 0, 0, $m, $d, $y);
		return date('Y-m-d H:i:s', $ts);
	}
}

if ( ! function_exists('date_php_to_mysql_end_time'))
{
	// normal date theke mysql_end_time er date format a convert korbe
	function date_php_to_mysql_end_time($dt)
	{
		$CI =& get_instance();
		// current_date_format ti config theke current 'account_date_format' data ti nibe
		$current_date_format = $CI->config->item('account_date_format');
		// default date, month, year set kora holo
		list($d, $m, $y) = array(0, 0, 0);

		// 'current_date_format' ti check korbe
		// 'current_date_format' onujayee date, month, year set korbe
		// 'current_date_format' er format chara onno format hole error message dekhabe
		switch ($current_date_format)
		{
			case 'dd/mm/yyyy':
				list($d, $m, $y) = explode('/', $dt);
				break;
			case 'mm/dd/yyyy':
				list($m, $d, $y) = explode('/', $dt);
				break;
			case 'yyyy/mm/dd':
				list($y, $m, $d) = explode('/', $dt);
				break;
			default:
				$CI->messages->add('Invalid date format. Check your account settings.', 'error');
				return "";
		}
		// mysql_end_time format a date ti prepare kore return kora hobe
		$ts = mktime("23", "59", "59", $m, $d, $y);
		return date('Y-m-d H:i:s', $ts);
	}
}

if ( ! function_exists('date_mysql_to_php'))
{
	// mysql format date theke php er date format a convert korbe
	function date_mysql_to_php($dt)
	{
		// date tike unix format a convert kora holo
		$ts = human_to_unix($dt);
		$CI =& get_instance();
		// current_date_format ti config theke current 'account_date_format' data ti nibe
		$current_date_format = $CI->config->item('account_date_format');

		// 'current_date_format' ti check korbe
		// 'current_date_format' onujayee date, month, year return korbe
		// 'current_date_format' er format chara onno format hole error message dekhabe
		switch ($current_date_format)
		{
		case 'dd/mm/yyyy':
			return date('d/m/Y', $ts);
			break;
		case 'mm/dd/yyyy':
			return date('m/d/Y', $ts);
			break;
		case 'yyyy/mm/dd':
			return date('Y/m/d', $ts);
			break;
		default:
			$CI->messages->add('Invalid date format. Check your account settings.', 'error');
			return "";
		}
		return;
	}
}

if ( ! function_exists('date_mysql_to_timestamp'))
{
	// mysql time ke timestamp a convert korlo
	function date_mysql_to_timestamp($dt)
	{
		return strtotime($dt);
	}
}

if ( ! function_exists('date_mysql_to_php_display'))
{
	// mysql format date theke display korar jonno php er date format a convert korbe
	function date_mysql_to_php_display($dt)
	{
		// date tike unix format a convert kora holo
		$ts = human_to_unix($dt);
		$CI =& get_instance();
		// current_date_format ti config theke current 'account_date_format' data ti nibe
		$current_date_format = $CI->config->item('account_date_format');

		// 'current_date_format' ti check korbe
		// 'current_date_format' onujayee date, month, year return korbe
		// 'current_date_format' er format chara onno format hole error message dekhabe
		switch ($current_date_format)
		{
		case 'dd/mm/yyyy':
			return date('d M Y', $ts);
			break;
		case 'mm/dd/yyyy':
			return date('M d Y', $ts);
			break;
		case 'yyyy/mm/dd':
			return date('Y M d', $ts);
			break;
		default:
			$CI->messages->add('Invalid date format. Check your account settings.', 'error');
			return "";
		}
		return;
	}
}

if ( ! function_exists('date_today_php'))
{
	function date_today_php()
	{
		$CI =& get_instance();

		/* Check for date beyond the current financial year range */
		// ajker system date ti 'todays_date' a set korlo
		$todays_date = date('Y-m-d 00:00:00');
		// account financial year start date ti config theke collect kora holo
		$fy_start = $CI->config->item('account_fy_start');
		// account financial year end date ti config theke collect kora holo
		$fy_end = $CI->config->item('account_fy_end');

		// account financial year start date ti jodi ajker ceye boro hoi tahole
		// normal date format a account financial year start date ti return kora hobe
		if ($CI->config->item('account_fy_start') > $todays_date)
			return date_mysql_to_php($fy_start);
		
		// account financial year end date ti jodi ajker ceye boro hoi tahole
		// normal date format a account financial year end date ti return kora hobe
		if ($CI->config->item('account_fy_end') < $todays_date)
			return date_mysql_to_php($fy_end);

		// current_date_format ti config theke current 'account_date_format' data ti nibe
		$current_date_format = $CI->config->item('account_date_format');

		// 'current_date_format' ti check korbe
		// 'current_date_format' onujayee date, month, year set korbe
		// 'current_date_format' er format chara onno format hole error message dekhabe
		switch ($current_date_format)
		{
		case 'dd/mm/yyyy':
			return date('d/m/Y');
			break;
		case 'mm/dd/yyyy':
			return date('m/d/Y');
			break;
		case 'yyyy/mm/dd':
			return date('Y/m/d');
			break;
		default:
			$CI->messages->add('Invalid date format. Check your account settings.', 'error');
			return "";
		}
		return;
	}
}

/* End of file date_helper.php */
/* Location: ./system/application/helpers/date_helper.php */
