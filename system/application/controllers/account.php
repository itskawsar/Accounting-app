<?php

// Account calss controller hisebe declare kora holo
class Account extends Controller {

	// account controller ti run korle index method ti by default run hobe
	// Details:
	// url= http://sitename.com/account/
	// url ti run korle codeigniter route kore by default index method ti run kore 
	// muloto URL ti: http://sitename.com/account/index
	function index()
	{
		// Ledger_model ti load kora holo. 
		// CodeIgniter class tike loading er somoy ledger_model a store kore
		// like: $ledger_model = new Ledger_model();
		// porobortite ei class er method gulo ke access pete $this->ledger_model->method_name() likhte hoi
		$this->load->model('Ledger_model');

		// template a "page_title" view er jonno set kora holo
		$this->template->set('page_title', 'Chart Of Accounts');
		// template a "nav_links" view er jonno set kora holo
		$this->template->set('nav_links', array('group/add' => 'Add Group', 'ledger/add' => 'Add Ledger'));

		/* Calculating difference in Opening Balance */
		// ledger model er maddome opening balnce er calculate korlo
		$total_op = $this->Ledger_model->get_diff_op_balance();
		// total opening balnce 0 er ceye beshi hole,
		// ekti error message set korbe
		if ($total_op > 0) {
			$this->messages->add('Difference in Opening Balance is Dr ' . convert_cur($total_op) . '.', 'error');

		// total opening balnce 0 er ceye beshi hole,
		// ekti error message set korbe
		} else if ($total_op < 0) {
			$this->messages->add('Difference in Opening Balance is Cr ' . convert_cur(-$total_op) . '.', 'error');
		}

		// template er maddhome system/application/views/account/index.php file ti open kora hocce
		// details dekhun: system/application/libraries/Template.php
		$this->template->load('template', 'account/index');
		return;
	}
}

/* End of file account.php */
/* Location: ./system/application/controllers/account.php */
