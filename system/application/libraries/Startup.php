<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Startup:: a class that is loaded everytime the application is accessed
 *
 * Setup all the initialization routines that the application uses in this
 * class. It is autoloaded evertime the application is accessed.
 *
 */

// Startup class ti application initialization korar somoy proyojonio dataguli collect kora, method gulike run kora ebong view file a data gulo display kora etc. ei class tir kaj
class Startup
{
	// ei method ti Startup class er __construct() er moto e kaj kore.
	function Startup()
	{
		// CodeIgniter er built-in feature gulo ei method a use korte declare kora holo
		$CI =& get_instance();

		// By default CodeIgniter runs all transactions in Strict Mode. When strict mode is enabled, if you are running multiple groups of transactions, if one group fails all groups will be rolled back. If strict mode is disabled, each group is treated independently, meaning a failure of one group will not affect any others.

		// database tran strict trict Mode ti disabled korar jonno nicer line ti likha holo
		$CI->db->trans_strict(FALSE);

		// general library ti load kora ei line tir kaj
		// CodeIgniter ei class tike loading er somoy general er data guli application a load kore
		// like: $general = new General();
		// porobortite ei class er method gulo ke access pete $this->general->method_name() likhte hoi
		$CI->load->library('general');

		/* Skip checking if accessing admin section*/
		// URL er first segment ti ADMIN kina eikhane check kortece
		if ($CI->uri->segment(1) == "admin")
			// URL er first segment ti ADMIN hole back korbe eikhan theke, na holo ei mathod er nicer code gulo RUN korbe
			return;

		/* Skip checking if accessing updated page */
		// URL er first segment ti UPDATE kina eikhane check kortece
		if ($CI->uri->segment(1) == "update")
			// URL er first segment ti UPDATE hole back korbe eikhan theke, na holo ei mathod er nicer code gulo RUN korbe
			return;

		/* Skip checking if accessing user section*/
		// URL er first segment ti USER kina eikhane check kortece
		if ($CI->uri->segment(1) == "user")
			// URL er first segment ti USER hole back korbe eikhan theke, na holo ei mathod er nicer code gulo RUN korbe
			return;

		/* Check if user is logged in */
		// SESSION a kono USER_NAME set kora ache kina ta eikhane check kora hocce
		if ( ! $CI->session->userdata('user_name'))
		{
			// jodi SESSION a USER_NAME na thake tahole login page a REDIRECT korbe.
			redirect('user/login');
			// back korbe
			return;
		}

		/* Reading database settings ini file */
		// SESSION a kono ACTIVE_ACCOUNT set kora ache kina ta eikhane check kora hocce
		if ($CI->session->userdata('active_account'))
		{
			/* Fetching database label details from session and checking the database ini file */
			// account data set kora ache kina check kore ACTIVE_ACCOUNT a current account er data guli store kore
			if ( ! $active_account = $CI->general->check_account($CI->session->userdata('active_account')))
			{
				// current account er data jodi blank hoi, tahole session data theke ACTIVE_ACCOUNT er data ti remove kore dibe
				$CI->session->unset_userdata('active_account');
				// USER controller er ACCOUNT method a ferot pathano hocce
				redirect('user/account');
				// return back
				return;
			}

			/* Preparing database settings */
			// ACTIVE ACCOUNT er details theke database information collect korce nicer line gulo
			// database HOSTNAME
			$db_config['hostname'] = $active_account['db_hostname'];
			// database PORT
			$db_config['hostname'] .= ":" . $active_account['db_port'];
			// database DATABASE_NAME
			$db_config['database'] = $active_account['db_name'];
			// database DATABASE_USER
			$db_config['username'] = $active_account['db_username'];
			// database DATABASE_PASSWORD
			$db_config['password'] = $active_account['db_password'];
			// database DATABASE_TYPE/DRIVER
			$db_config['dbdriver'] = "mysql";
			$db_config['dbprefix'] = "";
			$db_config['pconnect'] = FALSE;
			$db_config['db_debug'] = FALSE;
			$db_config['cache_on'] = FALSE;
			$db_config['cachedir'] = "";
			$db_config['char_set'] = "utf8";
			$db_config['dbcollat'] = "utf8_general_ci";
			// Uporer information gulo use kore database ti load kora hocce
			$CI->load->database($db_config, FALSE, TRUE);

			/* Checking for valid database connection */
			// database CONNECTION_ID check kora hocce
			if ( ! $CI->db->conn_id)
			{
				// CONNECTION_ID na paoya jaoyar karone ACTIVE_ACCOUNT data ti session theke UNSET kora hocce
				$CI->session->unset_userdata('active_account');
				// ekti ERROR MESSAGE set kora hocce
				$CI->messages->add('Error connecting to database server. Check whether database server is running.', 'error');
				// USER controller er ACCOUNT method a ferot pathano hocce
				redirect('user/account');
				// return back
				return;
			}
			/* Check for any database connection error messages */
			// DATABASE connecting er kono error message ace kina check kora hocce
			if ($CI->db->_error_message() != "")
			{
				// jodi kono error message paoya jaoyar karone ACTIVE_ACCOUNT data ti session theke UNSET kora hocce
				$CI->session->unset_userdata('active_account');
				// ekti error message set kora hocce
				$CI->messages->add('Error connecting to database server. ' . $CI->db->_error_message(), 'error');
				// USER controller er ACCOUNT method a ferot pathano hocce
				redirect('user/account');
				// return back
				return;
			}
		} else {
			// SESSION a ACTIVE_ACCOUNT er kono data na paoya gele, account selection er ekti MESSAGE set kore
			$CI->messages->add('Select a account.', 'error');
			// USER controller er ACCOUNT method a ferot pathano hocce
			redirect('user/account');
			// return back
			return;
		}

		/* Checking for valid database connection */
		// GENERAL library er 'check_database' method ti te database er table and version check kora hoy
		if ( ! $CI->general->check_database())
		{
			// database jodi check korte bertho hoi, tahole session data theke ACTIVE_ACCOUNT er data ti remove kore dibe
			$CI->session->unset_userdata('active_account');
			// USER controller er ACCOUNT method a ferot pathano hocce
			redirect('user/account');
			// return back
			return;
		}

		/* Loading account data */
		// nicer line ti dhara ekti sql statement generate hoi. Statement: 'SELECT * FROM SETINGS WHERE id=1 LIMIT 1'
		$CI->db->from('settings')->where('id', 1)->limit(1);
		// uporer SQL diye data ti grabe kore niye account_q te store kora holo
		$account_q = $CI->db->get();
		// account_q blank kina & account_d object format a store holo kina check kortece
		if ( ! ($account_d = $account_q->row()))
		{
			// uporer rules gulo FALSE hole, ekti ERROR MESSAGE set kore
			$CI->messages->add('Invalid account settings.', 'error');
			// USER controller er ACCOUNT method a ferot pathano hocce
			redirect('user/account');
			// return back
			return;
		}
		// config a field gulo set kore rakha hocce
		$CI->config->set_item('account_name', $account_d->name);
		$CI->config->set_item('account_address', $account_d->address);
		$CI->config->set_item('account_email', $account_d->email);
		$CI->config->set_item('account_fy_start', $account_d->fy_start);
		$CI->config->set_item('account_fy_end', $account_d->fy_end);
		$CI->config->set_item('account_currency_symbol', $account_d->currency_symbol);
		$CI->config->set_item('account_date_format', $account_d->date_format);
		$CI->config->set_item('account_timezone', $account_d->timezone);
		$CI->config->set_item('account_locked', $account_d->account_locked);
		$CI->config->set_item('account_database_version', $account_d->database_version);

		/* Load general application settings */
		// GENERAL library te settings gulo check kora hocce. SETTINGS er jonno data guli root_folder/config/settings/general.ini teke collect kora hoi
		$CI->general->check_setting();

		/* Load entry types */
		// settings er data guli jaiga moto set kora
		$CI->general->setup_entry_types();

		// return back
		return;
	}
}

/* End of file startup.php */
/* Location: ./system/application/libraries/startup.php */
