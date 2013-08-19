<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{	
		// system/application/models/ledger_model.php ফাইলটি লোড করা এবং Ledger_model নামে ইন্টেনশিয়েট করা
		$this->load->model('Ledger_model');
		// system/application/libraries/Accountlist.php ফাইলটি লোড করা এবং accountlist নামে ইন্টেনশিয়েট করল
		$this->load->library('accountlist');
		// পেজ টাইটেল টেমপ্লেটে সেট করল
		$this->template->set('page_title', 'Welcome to Webzash');

		/* Bank and Cash Ledger accounts */
		// ledgers টেবল থেকে type=1 রো টি চিহ্নিত করল
		$this->db->from('ledgers')->where('type', 1);
		// চিহ্নিত রোটি ডাটাবেজ থেকে নিয়ে আসল
		$bank_q = $this->db->get();
		// যদি রো শূন্য এর চেয়ে বেশি হয়
		if ($bank_q->num_rows() > 0)
		{
			foreach ($bank_q->result() as $row)
			{	
				// bank_cash_account নামে একটি নতুন এরে কী তৈরি করল এবং ডাটাবেজ থেকে নেয়া 
				// তথ্যগুলি এই কীতে সংরক্ষন করল
				$data['bank_cash_account'][] = array(
					'id' => $row->id,
					'name' => $row->name,
					'balance' => $this->Ledger_model->get_ledger_balance($row->id),
				);
			}
		} else { 
			// যদি রো শূন্য এর চেয়ে বেশি না হয়, তাহলে bank_cash_account নামের কী টি তে 
			// একটি খালি এরেকে সংরুক্ষান করল
			$data['bank_cash_account'] = array();
		}

		/* Calculating total of Assets, Liabilities, Incomes, Expenses */
		// Accountlist
		// Assets er total caltulation kore ber kora hocce
		// details dekhun: system/application/libraries/Accountlist.php
		$asset = new Accountlist();
		$asset->init(1);
		$data['asset_total'] = $asset->total;

		// Liabilities er total caltulation kore ber kora hocce
		// details dekhun: system/application/libraries/Accountlist.php
		$liability = new Accountlist();
		$liability->init(2);
		$data['liability_total'] = $liability->total;

		// Incomes er total caltulation kore ber kora hocce
		// details dekhun: system/application/libraries/Accountlist.php
		$income = new Accountlist();
		$income->init(3);
		$data['income_total'] = $income->total;

		// Expenses er total caltulation kore ber kora hocce
		// details dekhun: system/application/libraries/Accountlist.php
		$expense = new Accountlist();
		$expense->init(4);
		$data['expense_total'] = $expense->total;

		/* Getting Log Messages */
		// notun ekti log message collect korbe
		$data['logs'] = $this->logger->read_recent_messages();
		// template er maddhome system/application/views/welcome.php file ti open kora hocce
		// details dekhun: system/application/libraries/Template.php
		$this->template->load('template', 'welcome', $data);
		return;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
