<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General {
	var $error_messages = array();

	function General()
	{
		return;
	}

	/* Check format of config/accounts ini files */
	// account information check kora ei method er kaj
	// ACCOUNT_NAME param ti pass korle accounts config theke account name er information gulo collect kore
	// exp. ACCOUNT_NAME jodi KAWSAR hoi, tahole kawsar.ini file ti theke data collect kore RETURN kore
	// file location hobe: root_folder/config/accounts/kawsar.ini
	function check_account($account_name)
	{
		$CI =& get_instance();

		// ini file load kora hocce
		// file location: root_folder/config/accounts/kawsar.ini
		$ini_file = $CI->config->item('config_path') . "accounts/" . $account_name . ".ini";

		/* Check if database ini file exists */
		// root_folder/config/accounts/kawsar.ini file tir property / file ti jodi exist na hoi
		// tahole ekti error message diye FALSE return korbe
		if ( ! get_file_info($ini_file))
		{
			$CI->messages->add('Account settings file is missing.', 'error');
			return FALSE;
		}

		/* Parsing database ini file */
		// kawsar.ini file ti theke data collect korbe
		$account_data = parse_ini_file($ini_file);
		// file theke data collect korte bertho hole ekti error message diye FALSE return korbe
		if ( ! $account_data)
		{
			$CI->messages->add('Invalid account settings.', 'error');
			return FALSE;
		}

		/* Check if all needed variables are set in ini file */
		// database host name jodi config file ti te na thake, tahole ekti error message diye FALSE return korbe
		if ( ! isset($account_data['db_hostname']))
		{
			$CI->messages->add('Hostname missing from account settings file.', 'error');
			return FALSE;
		}
		// database port jodi config file ti te na thake, tahole ekti error message diye FALSE return korbe
		if ( ! isset($account_data['db_port']))
		{
			$CI->messages->add('Port missing from account setting file. Default MySQL port is 3306.', 'error');
			return FALSE;
		}
		// database name jodi config file ti te na thake, tahole ekti error message diye FALSE return korbe
		if ( ! isset($account_data['db_name']))
		{
			$CI->messages->add('Database name missing from account setting file.', 'error');
			return FALSE;
		}
		// database username jodi config file ti te na thake, tahole ekti error message diye FALSE return korbe
		if ( ! isset($account_data['db_username']))
		{
			$CI->messages->add('Database username missing from account setting file.', 'error');
			return FALSE;
		}
		// database password jodi config file ti te na thake, tahole ekti error message diye FALSE return korbe
		if ( ! isset($account_data['db_password']))
		{
			$CI->messages->add('Database password missing from account setting file.', 'error');
			return FALSE;
		}
		// data guli return korbe
		return $account_data;
	}

	/* Check for valid account database */
	// CURRENT ACCOUNT tir jonno database check kora ei method er kaj
	function check_database()
	{
		$CI =& get_instance();

		/* Checking for valid database connection */
		// kono DATABASE connection active ache kina check kora hocce
		if ($CI->db->conn_id)
		{
			/* Checking for valid database name, username, password */
			// ekti QUERY run kore check kora hocce database ti VALID kina
			if ($CI->db->query("SHOW TABLES"))
			{
				/* Check for valid webzash database */
				// URL er first segment ti UPDATE kina eikhane check kortece
				if ($CI->uri->segment(1) != "update")
				{
					/* check for valid settings table */
					// settings tabel ke DESCRIBE kora holo
					$valid_settings_q = mysql_query('DESC settings');

					// SETTINGS DESCRIBE er query check kora holo
					if ( ! $valid_settings_q)
					{
						// SETTINGS DESCRIBE er query TRUE na hole, ekti ERROR MESSAGE set kore
						$CI->messages->add('Invalid account database. Table "settings" missing.', 'error');
						// return back
						return FALSE;
					}
					// database version check kore
					$this->check_database_version();

					// database er TABLE NAME guli prepare kore
					$table_names = array('groups', 'ledgers', 'entry_types', 'entries', 'entry_items', 'tags', 'logs', 'settings');
					// foreach loop
					foreach ($table_names as $id => $tbname)
					{
						// protiti table er DESCRIBEd information gulo 'valid_db_q' te store kora
						$valid_db_q = mysql_query('DESC ' . $tbname);
						// table DESCRIBE jodi valid na hoy
						if ( ! $valid_db_q)
						{
							// database er table DESCRIBE ti jodi VALID na hoi, tahole ekti ERROR MESSAGE set korbe
							$CI->messages->add('Invalid account database. Table "' . $tbname . '" missing.', 'error');
							// return back
							return FALSE;
						}
					}
				}
			} else {
				// database ti jodi VALID na hoi, tahole ekti ERROR MESSAGE set korbe
				$CI->messages->add('Invalid database connection settings. Check whether the provided database name, username and password are valid.', 'error');
				// return back
				return FALSE;
			}
		} else {
			// database er CONNECTION_ID jodi VALID na hoi, tahole ekti ERROR MESSAGE set korbe
			$CI->messages->add('Cannot connect to database server. Check whether database server is running.', 'error');
			// return back
			return FALSE;
		}
		// return back
		return TRUE;
	}

	/* Check config/settings/general.ini file */
	function check_setting()
	{
		$CI =& get_instance();

		// setting_ini_file a root_folder/config/settings/general.ini set korbe 
		$setting_ini_file = $CI->config->item('config_path') . "settings/general.ini";

		/* Set default values */
		// aro kicu default item set kora holo
		$CI->config->set_item('row_count', "20");
		$CI->config->set_item('log', "1");

		/* Check if general application settings ini file exists */
		// setting_ini_file er file ti exist kina check kora hocce
		if (get_file_info($setting_ini_file))
		{
			/* Parsing general application settings ini file */
			// setting_ini_file er file ti theke cur_setting a array format a data store kore
			$cur_setting = parse_ini_file($setting_ini_file);
			// file ti invalid kina check kora hocce
			if ($cur_setting)
			{
				// cur_setting a row_count name kono key exist kina check kora hocce
				if (isset($cur_setting['row_count']))
				{
					// tahole config a row_count ti set korbe
					$CI->config->set_item('row_count', $cur_setting['row_count']);
				}
				// cur_setting a log name kono key exist kina check kora hocce
				if (isset($cur_setting['log']))
				{
					// tahole config a log ti set korbe
					$CI->config->set_item('log', $cur_setting['log']);
				}
			}
		}
	}

	// user_data guli check kore ini file theke array format a return kore ei method ti
	function check_user($user_name)
	{
		$CI =& get_instance();

		/* User validation */
		// user_name jodi "kawsar" hoi, tahole root_folder/config/users/kawsar.ini set korbe ini_file a
		$ini_file = $CI->config->item('config_path') . "users/" . $user_name . ".ini";

		/* Check if user ini file exists */
		// ini_file er file ti exist kina check korce
		if ( ! get_file_info($ini_file))
		{
			// ini_file er file ti exist na hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('User does not exists.', 'error');
			//return back
			return FALSE;
		}

		/* Parsing user ini file */
		// ini_file er dataguli array format a user_data te store kora
		$user_data = parse_ini_file($ini_file);
		// ini_file ti valid kina check korce
		if ( ! $user_data)
		{
			// ini_file ti valid na hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('Invalid user file.', 'error');
			//return back
			return FALSE;
		}

		// user_data te 'username' set kora ache kina check kora hocce
		if ( ! isset($user_data['username']))
		{
			// user_data te 'username' set kora na hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('Username missing from user file.', 'error');
			//return back
			return FALSE;
		}
		// user_data te 'password' set kora ache kina check kora hocce
		if ( ! isset($user_data['password']))
		{
			// user_data te 'password' set kora na hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('Password missing from user file.', 'error');
			//return back
			return FALSE;
		}
		// user_data te 'status' set kora ache kina check kora hocce
		if ( ! isset($user_data['status']))
		{
			// user_data te 'status' set kora na hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('Status missing from user file.', 'error');
			//return back
			return FALSE;
		}
		// user_data te 'role' set kora ache kina check kora hocce
		if ( ! isset($user_data['role']))
		{
			// user_data te 'role' set kora na hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('Role missing from user file. Defaulting to "guest" role.', 'error');
			$user_data['role'] = 'guest';
		}
		// user_data te 'accounts' set kora ache kina check kora hocce
		if ( ! isset($user_data['accounts']))
		{
			// user_data te 'accounts' set kora na hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('Accounts missing from user file.', 'error');
		}

		// user_data array ti return kora holo
		return $user_data;
	}

	// database version check kore ei method ti
	function check_database_version()
	{
		$CI =& get_instance();

		// URL er first segment ti UPDATE kina eikhane check kortece
		if ($CI->uri->segment(1) == "update")
			// return back
			return;

		/* Loading account data */
		// nicer line ti dhara ekti sql statement generate hoi. Statement: 'SELECT * FROM settings WHERE id=1 LIMIT 1'
		$CI->db->from('settings')->where('id', 1)->limit(1);
		// uporer SQL diye data ti grabe kore niye account_q te store kora holo
		$account_q = $CI->db->get();
		// account_q blank kina check kortece
		if ( ! ($account_d = $account_q->row()))
		{
			// account_q blank hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('Invalid account settings.', 'error');
			// USER controller er ACCOUNT method a ferot pathano hocce
			redirect('user/account');
			// return back
			return;
		}

		// database er version config er ceye coto kina ta check kora hocce
		if ($account_d->database_version < $CI->config->item('required_database_version'))
		{
			// database er version config er ceye coto hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('You need to updated the account database before continuing. Click ' . anchor('update', 'here', array('ttile' => 'Click here to update account database')) . ' to update.', 'error');
			// USER controller er ACCOUNT method a ferot pathano hocce
			redirect('user/account');
			// return back
			return;
		} else if ($account_d->database_version > $CI->config->item('required_database_version')) {
			// database er version config er ceye boro hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('You need to updated the application version from <a href="http://webzash.org" target="_blank">http://webzash.org<a/> before continuing.', 'error');
			// USER controller er ACCOUNT method a ferot pathano hocce
			redirect('user/account');
			// return back
			return;
		}
	}

	// settings er data guli jaiga moto set kora
	function setup_entry_types()
	{
		$CI =& get_instance();
		// nicer line ti dhara ekti sql statement generate hoi. Statement: 'SELECT * FROM entry_types ORDER BY id asc'
		$CI->db->from('entry_types')->order_by('id', 'asc');
		// uporer SQL diye data ti grabe kore niye entry_types te store kora holo
		$entry_types = $CI->db->get();
		// entry_types blank kina check kortece
		if ($entry_types->num_rows() < 1)
		{
			// entry_types blank hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('You need to create a entry type before you can create any entries.', 'error');
		}
		// entry_type_config array hisebe initialized kora holo
		$entry_type_config = array();
		// entry_types ke loop a probesh korano holo
		foreach ($entry_types->result() as $id => $row)
		{
			// entry_type_config a data guli ke store kora holo
			$entry_type_config[$row->id] = array(
				'label' => $row->label,
				'name' => $row->name,
				'description' => $row->description,
				'base_type' => $row->base_type,
				'numbering' => $row->numbering,
				'prefix' => $row->prefix,
				'suffix' => $row->suffix,
				'zero_padding' => $row->zero_padding,
				'bank_cash_ledger_restriction' => $row->bank_cash_ledger_restriction,
			);
		}
		// config a account_entry_types a dataguli set kora holo
		$CI->config->set_item('account_entry_types', $entry_type_config);
	}
}

/* End of file General.php */
/* Location: ./system/application/libraries/General.php */
