<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Converts D/C to Dr / Cr
 *
 * Covnerts the D/C received from database to corresponding
 * Dr/Cr value for display.
 *
 * @access	public
 * @param	string	'd' or 'c' from database table
 * @return	string
 */	
if ( ! function_exists('convert_dc'))
{
	// "D" label ke "Dr" a
	// "C" lable ke "Cr" a
	// onno label ke "Error" a convert korbe
	function convert_dc($label)
	{
		if ($label == "D")
			return "Dr";
		else if ($label == "C")
			return "Cr";
		else
			return "Error";
	}
}

/**
 * Converts amount to Dr or Cr Value
 *
 * Covnerts the amount to 0 or Dr or Cr value for display
 *
 * @access	public
 * @param	float	amount for display
 * @return	string
 */	
if ( ! function_exists('convert_amount_dc'))
{
	// ei method ti 'amount' ke Dr / Cr a convert korbe
	function convert_amount_dc($amount)
	{
		// jodi "amount" = D hoi tahole 0 return korbe
		if ($amount == "D")
			return "0";

		// jodi "amount" 0 er choto hoi tahole negetive value return korbe
		else if ($amount < 0)
			return "Cr " . convert_cur(-$amount);

		// jodi "amount" 0 er boro hoi tahole positive value return korbe
		else
			return "Dr " . convert_cur($amount);
	}
}

/**
 * Converts Opening balance amount to Dr or Cr Value
 *
 * Covnerts the Opening balance amount to 0 or Dr or Cr value for display
 *
 * @access	public
 * @param	amount
 * @param	debit or credit
 * @return	string
 */
if ( ! function_exists('convert_opening'))
{
	// ei method ti 'amount' ti Dr naki Cr ta return korbe
	function convert_opening($amount, $dc)
	{
		// jodi "amount" = 0 hoi tahole 0 return korbe
		if ($amount == 0)
			return "0";

		// jodi "amount" = D hoi tahole Dr string soho value return korbe
		else if ($dc == 'D')
			return "Dr " . convert_cur($amount);

		// jodi "amount" = C hoi tahole Cr string soho value return korbe
		else
			return "Cr " . convert_cur($amount);
	}
}

if ( ! function_exists('convert_cur'))
{
	// 'amount' ke number format a return korbe
	// jodi 'amount' ti "5556.44632" hoi, tahole "5556.45" return korbe
	function convert_cur($amount)
	{
		return number_format($amount, 2, '.', '');
	}
}

/**
 * Return the value of variable is set
 *
 * Return the value of varaible is set else return empty string
 *
 * @access	public
 * @param	a varaible
 * @return	string value
 */	
if ( ! function_exists('print_value'))
{
	// value NULL hole default value return korbe
	// ta na hole value return korbe
	function print_value($value = NULL, $default = "")
	{
		if (isset($value))
			return $value;
		else
			return $default;
	}
}

/**
 * Return Entry Type information
 *
 * @access	public
 * @param	int entry type id
 * @return	array
 */
if ( ! function_exists('entry_type_info'))
{
	// account er entry type info collect korbe
	function entry_type_info($entry_type_id)
	{
		$CI =& get_instance();
		// 'account_entry_types' set kora thakbe config a
		// config file er location: system/application/config/config.php
		// or Location: config/*
		// karon 'account_entry_types' ei item ti loaded config a check korbe
		$entry_type_all = $CI->config->item('account_entry_types');

		// 'account_entry_types' config a jodi key ti exist hoi tahole notun ekta array return korbe config er value soho
		if ($entry_type_all[$entry_type_id])
		{
			return array(
				'id' => $entry_type_all[$entry_type_id],
				'label' => $entry_type_all[$entry_type_id]['label'],
				'name' => $entry_type_all[$entry_type_id]['name'],
				'numbering' => $entry_type_all[$entry_type_id]['numbering'],
				'prefix' => $entry_type_all[$entry_type_id]['prefix'],
				'suffix' => $entry_type_all[$entry_type_id]['suffix'],
				'zero_padding' => $entry_type_all[$entry_type_id]['zero_padding'],
				'bank_cash_ledger_restriction' => $entry_type_all[$entry_type_id]['bank_cash_ledger_restriction'],
			);

		// 'account_entry_types' config a jodi key ti exist NA hoi tahole notun ekta array return korbe blank/default value soho
		} else {
			return array(
				'id' => $entry_type_all[$entry_type_id],
				'label' => '',
				'name' => '(Unkonwn)',
				'numbering' => 1,
				'prefix' => '',
				'suffix' => '',
				'zero_padding' => 0,
				'bank_cash_ledger_restriction' => 5,
			);
		}
	}
}

/**
 * Return Entry Type Id from Entry Type Name
 *
 * @access	public
 * @param	string entry type name
 * @return	int entry type id
 */
if ( ! function_exists('entry_type_name_to_id'))
{

	function entry_type_name_to_id($entry_type_name)
	{
		$CI =& get_instance();
		// 'account_entry_types' set kora thakbe config a
		// config file er location: system/application/config/config.php
		// or Location: config/*
		// karon 'account_entry_types' ei item ti loaded config a check korbe
		$entry_type_all = $CI->config->item('account_entry_types');
		foreach ($entry_type_all as $id => $row)
		{
			// jodi config er label field ti entry_type_name er moto hoy tahole config item tir ID return korbe
			// prothom ID ti peye gele loop break korbe
			if ($row['label'] == $entry_type_name)
			{
				return $id;
				break;
			}
		}
		// jodi label ti paoya na jai tahole FALSE retun korbe
		return FALSE;
	}
}

/**
 * Converts Entry number to proper entry prefix formats
 *
 * @access	public
 * @param	int entry type id
 * @return	string
 */
if ( ! function_exists('full_entry_number'))
{
	function full_entry_number($entry_type_id, $entry_number)
	{
		$CI =& get_instance();
		// 'account_entry_types' set kora thakbe config a
		// config file er location: system/application/config/config.php
		// or Location: config/*
		// karon 'account_entry_types' ei item ti loaded config a check korbe
		$entry_type_all = $CI->config->item('account_entry_types');

		$return_html = "";
		// 'account_entry_types' config a jodi key ti exist NA hoi tahole 'entry_number' ti "return_html" a set korbe
		if ( ! $entry_type_all[$entry_type_id])
		{
			$return_html = $entry_number;

		// 'account_entry_types' config a jodi key ti exist hoi tahole 'entry_number' ti "return_html" a set korbe
		} else {
			// prefix, entry_number & serfix niye ekti string banabe
			// majkhane entry_number ke left 'zero_padding' onujayee a 0 bosabe
			$return_html = $entry_type_all[$entry_type_id]['prefix'] . str_pad($entry_number, $entry_type_all[$entry_type_id]['zero_padding'], '0', STR_PAD_LEFT) . $entry_type_all[$entry_type_id]['suffix'];
		}
		// "return_html" er value blank/khali na hoi tahole "return_html" return korbe
		// onnothay ekti space/" " return korbe
		if ($return_html)
			return $return_html;
		else
			return " ";
	}
}

/**
 * Floating Point Operations
 *
 * Multiply the float by 100, convert it to integer,  
 * Perform the integer operation and then divide the result
 * by 100 and return the result
 *
 * @access	public
 * @param	float	number 1
 * @param	float	number 2
 * @param	string	operation to be performed
 * @return	float	result of the operation
 */	
if ( ! function_exists('float_ops'))
{
	function float_ops($param1 = 0, $param2 = 0, $op = '')
	{
		// default value set korbe
		$result = 0;
		// param gulike 100 dara ghun korbe
		$param1 = $param1 * 100;
		$param2 = $param2 * 100;
		// dosomik er ongso guli baad dibe
		$param1 = (int)round($param1, 0);
		$param2 = (int)round($param2, 0);

		// "op" er sign check korbe
		// "op" er sign onujayee calculation korbe
		// jodi "op" er sign "-" / "+" hoi, tahole calculation ti hobe:
		// result = 1000 - 900
		// jodi "op" er sign "==" / "!=" / "<" / ">" hoi, tahole TRUE/FALSE return korbe
		switch ($op)
		{
		case '+':
			$result = $param1 + $param2;
			break;
		case '-':
			$result = $param1 - $param2;
			break;
		case '==':
			if ($param1 == $param2)
				return TRUE;
			else
				return FALSE;
			break;
		case '!=':
			if ($param1 != $param2)
				return TRUE;
			else
				return FALSE;
			break;
		case '<':
			if ($param1 < $param2)
				return TRUE;
			else
				return FALSE;
			break;
		case '>':
			if ($param1 > $param2)
				return TRUE;
			else
				return FALSE;
			break;

		}
		$result = $result/100;
		return $result;
	}
}

/* End of file custom_helper.php */
/* Location: ./system/application/helpers/custom_helper.php */
