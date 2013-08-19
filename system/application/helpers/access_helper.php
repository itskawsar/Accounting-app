<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Check if the currently logger in user has the necessary permissions
 * to permform the given action
 *
 * Valid permissions strings are given below :
 *
 * 'view entry'
 * 'create entry'
 * 'edit entry'
 * 'delete entry'
 * 'print entry'
 * 'email entry'
 * 'download entry'
 * 'create ledger'
 * 'edit ledger'
 * 'delete ledger'
 * 'create group'
 * 'edit group'
 * 'delete group'
 * 'create tag'
 * 'edit tag'
 * 'delete tag'
 * 'view reports'
 * 'view log'
 * 'clear log'
 * 'change account settings'
 * 'cf account'
 * 'backup account'
 * 'administer'
 */

if ( ! function_exists('check_access'))
{
	function check_access($action_name)
	{
		$CI =& get_instance();
		// logged in user er 'user_role' data ti session theke collect korbe
		$user_role = $CI->session->userdata('user_role');
		// manager access guli
		$permissions['manager'] = array(
			'view entry',
			'create entry',
			'edit entry',
			'delete entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
			'delete ledger',
			'create group',
			'edit group',
			'delete group',
			'create tag',
			'edit tag',
			'delete tag',
			'view reports',
			'view log',
			'clear log',
			'change account settings',
			'cf account',
			'backup account',
		);
		// accountant access guli
		$permissions['accountant'] = array(
			'view entry',
			'create entry',
			'edit entry',
			'delete entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
			'delete ledger',
			'create group',
			'edit group',
			'delete group',
			'create tag',
			'edit tag',
			'delete tag',
			'view reports',
			'view log',
			'clear log',
		);
		// dataentry access guli
		$permissions['dataentry'] = array(
			'view entry',
			'create entry',
			'edit entry',
			'delete entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
		);
		// guest access guli
		$permissions['guest'] = array(
			'view entry',
			'print entry',
			'email entry',
			'download entry',
		);

		// 'user_role' khuje na paoya gele return korbe
		if ( ! isset($user_role))
			return FALSE;

		/* If user is administrator then always allow access */
		// "administrator" ke sokol access deya holo
		if ($user_role == "administrator")
			return TRUE;

		// jodi 'user role' er jonno permissions na set kora thake tahole o return korbe
		if ( ! isset($permissions[$user_role]))
			return FALSE;

		// jodi 'user role' er permissions a action ti khuje paoya jai tahole access dibe
		// khuje na paoya gele access pabe na
		if (in_array($action_name, $permissions[$user_role]))
			return TRUE;
		else
			return FALSE;
	}
}

/* End of file access_helper.php */
/* Location: ./system/application/helpers/access_helper.php */
