<?php

class Ledger_model extends Model {
	// constractor
	function Ledger_model()
	{
		parent::Model();
	}

	// sokol ledgers gulo collect kora ei method tir kaj
	function get_all_ledgers()
	{
		// options ke ARRAY hisebe initialized korbe
		$options = array();
		// optoins 0 key te default value set kora holo, ei options er value gulo drop down er jonno kaje lagbe
		$options[0] = "(Please Select)";
		// "SELECT * FROM ledgers ORDER BY name ASC" statement ti generate korbe
		$this->db->from('ledgers')->order_by('name', 'asc');
		// sql ti RUN korbe
		$ledger_q = $this->db->get();
		// data guli ke ARRAY te declare kore loop a bosano holo
		foreach ($ledger_q->result() as $row)
		{
			// options er key hisebe table er id & value hisebe table er name field er data gulo store kora hocce
			$options[$row->id] = $row->name;
		}
		// options er data gulo ke RETURN kora holo
		return $options;
	}

	function get_all_ledgers_bankcash()
	{
		// options ke ARRAY hisebe initialized korbe
		$options = array();
		// optoins 0 key te default value set kora holo, ei options er value gulo drop down er jonno kaje lagbe
		$options[0] = "(Please Select)";
		// "SELECT * FROM ledgers WHERE type = 1 ORDER BY name ASC" statement ti generate korbe
		$this->db->from('ledgers')->where('type', 1)->order_by('name', 'asc');
		// sql ti RUN korbe
		$ledger_q = $this->db->get();
		// data guli ke ARRAY te declare kore loop a bosano holo
		foreach ($ledger_q->result() as $row)
		{
			// options er key hisebe table er id & value hisebe table er name field er data gulo store kora hocce
			$options[$row->id] = $row->name;
		}
		// options er data gulo ke RETURN kora holo
		return $options;
	}

	function get_all_ledgers_nobankcash()
	{
		// options ke ARRAY hisebe initialized korbe
		$options = array();
		// optoins 0 key te default value set kora holo, ei options er value gulo drop down er jonno kaje lagbe
		$options[0] = "(Please Select)";
		// "SELECT * FROM ledgers WHERE type != 1 ORDER BY name ASC" statement ti generate korbe
		$this->db->from('ledgers')->where('type !=', 1)->order_by('name', 'asc');
		// sql ti RUN korbe
		$ledger_q = $this->db->get();
		// data guli ke ARRAY te declare kore loop a bosano holo
		foreach ($ledger_q->result() as $row)
		{
			// options er key hisebe table er id & value hisebe table er name field er data gulo store kora hocce
			$options[$row->id] = $row->name;
		}
		// options er data gulo ke RETURN kora holo
		return $options;
	}

	function get_all_ledgers_reconciliation()
	{
		// options ke ARRAY hisebe initialized korbe
		$options = array();
		// optoins 0 key te default value set kora holo, ei options er value gulo drop down er jonno kaje lagbe
		$options[0] = "(Please Select)";
		// "SELECT * FROM ledgers WHERE reconciliation = 1 ORDER BY name ASC" statement ti generate korbe
		$this->db->from('ledgers')->where('reconciliation', 1)->order_by('name', 'asc');
		// sql ti RUN korbe
		$ledger_q = $this->db->get();
		// data guli ke ARRAY te declare kore loop a bosano holo
		foreach ($ledger_q->result() as $row)
		{
			// options er key hisebe table er id & value hisebe table er name field er data gulo store kora hocce
			$options[$row->id] = $row->name;
		}
		// options er data gulo ke RETURN kora holo
		return $options;
	}

	function get_name($ledger_id)
	{
		// jodi ledger_id = 5 hoi, "SELECT * FROM ledgers WHERE id = 5 LIMIT 1" statement ti generate korbe
		$this->db->from('ledgers')->where('id', $ledger_id)->limit(1);
		// sql ti RUN korbe
		$ledger_q = $this->db->get();
		// kono row exist kina
		if ($ledger = $ledger_q->row())
			// exist hole table er name field ti return korbe
			return $ledger->name;
		else
			// error message return korbe
			return "(Error)";
	}

	function get_entry_name($entry_id, $entry_type_id)
	{
		/* Selecting whether to show debit side Ledger or credit side Ledger */
		// config item theke 'account_entry_types' er data guli current_entry_type collect korbe
		$current_entry_type = entry_type_info($entry_type_id);
		// default C set korbe ledger_type a
		$ledger_type = 'C';

		// current_entry_type a bank_cash_ledger_restriction == 3 kina ta check korbe
		if ($current_entry_type['bank_cash_ledger_restriction'] == 3)
			// jodi D set korbe ledger_type a
			$ledger_type = 'D';

		// jodi entry_id = 303 & entry_items.dc = 505 hoi, 
		// "SELECT ledgers.name AS name FROM entry_items JOIN ledgers WHERE entry_items.ledger_id = ledgers.id AND entry_items.entry_id = 303 AND entry_items.dc = 505" statement ti generate korbe
		$this->db->select('ledgers.name as name');
		$this->db->from('entry_items')->join('ledgers', 'entry_items.ledger_id = ledgers.id')->where('entry_items.entry_id', $entry_id)->where('entry_items.dc', $ledger_type);
		// sql ti RUN korbe
		$ledger_q = $this->db->get();
		// kono row exist kina
		if ( ! $ledger = $ledger_q->row())
		{
			// exist na korle error message return korbe
			return "(Invalid)";
		} else {
			// row 1 er ceye odhik hole ledger_multiple TURE set korbe
			$ledger_multiple = ($ledger_q->num_rows() > 1) ? TRUE : FALSE;
			$html = '';
			// ledger_multiple TRUE hole
			if ($ledger_multiple)
				// ledger_multiple TURE hole ekti enchor tag genarate korbe
				// <a href="http://base_url/entry/view/current_entry_type_label/303/" title="View current_entry_type_name Entry" class="anchor-link-a">(ledger_name)</a>
				$html .= anchor('entry/view/' . $current_entry_type['label'] . "/" . $entry_id, "(" . $ledger->name . ")", array('title' => 'View ' . $current_entry_type['name'] . ' Entry', 'class' => 'anchor-link-a'));
			else
				// ledger_multiple FALSE hole ekti enchor tag genarate korbe
				// <a href="http://base_url/entry/view/current_entry_type_label/303/" title="View current_entry_type_name Entry" class="anchor-link-a">ledger_name</a>
				$html .= anchor('entry/view/' . $current_entry_type['label'] . "/" . $entry_id, $ledger->name, array('title' => 'View ' . $current_entry_type['name'] . ' Entry', 'class' => 'anchor-link-a'));
			// anchor tag ti return korbe
			return $html;
		}
		// return back
		return;
	}

	function get_opp_ledger_name($entry_id, $entry_type_label, $ledger_type, $output_type)
	{
		$output = '';
		// ledger_type value D ace kina check korbe
		if ($ledger_type == 'D')
			// opp_ledger_type a C set korbe
			$opp_ledger_type = 'C';
		else
			// opp_ledger_type a C set korbe
			$opp_ledger_type = 'D';

		// jodi entry_id = 5 hoi & opp_ledger_type = xyz hoi,
		// "SELECT * FROM entry_items WHERE entry_id = 5 AND dc = xyz" statement ti generate korbe
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('dc', $opp_ledger_type);
		// sql ti RUN korbe
		$opp_entry_name_q = $this->db->get();
		// row exist korbe kina
		if ($opp_entry_name_d = $opp_entry_name_q->row())
		{
			// ledger name ti collect kore
			$opp_ledger_name = $this->get_name($opp_entry_name_d->ledger_id);
			// query te kono data row exist kore kina
			if ($opp_entry_name_q->num_rows() > 1)
			{
				// jodi kore, output_type = html kina
				if ($output_type == 'html')
					// <a href="http://base_url/entry/view/entry_type_label/303/" title="View Entry" class="anchor-link-a">(opp_ledger_name)</a>
					$output = anchor('entry/view/' . $entry_type_label . '/' . $entry_id, "(" . $opp_ledger_name . ")", array('title' => 'View ' . ' Entry', 'class' => 'anchor-link-a'));
				else 
					// (opp_ledger_name)
					$output = "(" . $opp_ledger_name . ")";
			} else {
				// data exist na korle, output_type = html kina
				if ($output_type == 'html')
					// <a href="http://base_url/entry/view/entry_type_label/303/" title="View Entry" class="anchor-link-a">opp_ledger_name</a>
					$output = anchor('entry/view/' . $entry_type_label . '/' . $entry_id, $opp_ledger_name, array('title' => 'View ' . ' Entry', 'class' => 'anchor-link-a'));
				else
					// opp_ledger_name
					$output = $opp_ledger_name;
			}
		}
		// output return korbe
		return $output;
	}

	function get_ledger_balance($ledger_id)
	{
		// op_bal & op_bal_type er value set korlo
		// get_op_balance($ledger_id) te "SELECT * FROM ledgers WHERE id = $ledger_id LIMIT 1" statement tir value run kore. Tokhon akti array return kore. 
		// array er value guli holo op_balance, op_balance_dc
		list ($op_bal, $op_bal_type) = $this->get_op_balance($ledger_id);

		// dr_total er value set korlo
		// get_dr_total($ledger_id) te 'entry_items' table er 'amount' field er jogfol drtotal hisebe return korbe. 
		// "ledger_id" ti na thakle 0 return kore. 
		$dr_total = $this->get_dr_total($ledger_id);
		// cr_total er value set korlo
		// get_dr_total($ledger_id) te 'entry_items' table er 'amount' field er jogfol drtotal hisebe return korbe.
		// "ledger_id" ti na thakle 0 return kore. 
		$cr_total = $this->get_cr_total($ledger_id);

		// $total = $dr_total - $cr_total
		// total = 10 - 4 (jodi $dr_total = 10 & $cr_total = 4 hoi)
		$total = float_ops($dr_total, $cr_total, '-');

		// 'ledgers' table a 'op_balance_dc' field ti jodi "D" hoi
		if ($op_bal_type == "D")
			// $total = $total + $op_bal
			// total = 10 + 4 (jodi $total = 10 & $op_bal = 4 hoi)
			$total = float_ops($total, $op_bal, '+');
		else
			// $total = $total - $op_bal
			// total = 10 - 4 (jodi $total = 10 & $op_bal = 4 hoi)
			$total = float_ops($total, $op_bal, '-');

		// total return korbe
		return $total;
	}

	// get_op_balance($ledger_id) te "SELECT * FROM ledgers WHERE id = $ledger_id LIMIT 1" statement tir value run kore. Tokhon akti array return kore. 
	// array er value guli holo op_balance, op_balance_dc
	function get_op_balance($ledger_id)
	{
		// jodi NULL hoi, "SELECT * FROM ledgers WHERE id = $ledger_id LIMIT 1" statement ti generate korbe
		$this->db->from('ledgers')->where('id', $ledger_id)->limit(1);
		// sql ti RUN korbe
		$op_bal_q = $this->db->get();
		if ($op_bal = $op_bal_q->row())
			// 'op_balance' & 'op_balance_dc' field er data niye ekti ARRAY return korbe
			return array($op_bal->op_balance, $op_bal->op_balance_dc);
		else
			// 'ledger_id' ti 'ledgers' table a na thakle default value return korbe
			// 'op_balance' = "0" & 'op_balance_dc' = "D" niye ekti ARRAY return korbe
			return array(0, "D");
	}

	function get_diff_op_balance()
	{
		/* Calculating difference in Opening Balance */
		$total_op = 0;
		// jodi NULL hoi, "SELECT * FROM ledgers ORDER BY id ASC" statement ti generate korbe
		$this->db->from('ledgers')->order_by('id', 'asc');
		// sql ti RUN korbe
		$ledgers_q = $this->db->get();
		// data guli ke ARRAY te declare kore loop a bosano holo
		foreach ($ledgers_q->result() as $row)
		{
			// op_bal & op_bal_type er value set korlo
			// get_op_balance($ledger_id) te "SELECT * FROM ledgers WHERE id = $ledger_id LIMIT 1" statement tir value run kore. Tokhon akti array return kore. 
			// array er value guli holo op_balance, op_balance_dc
			list ($opbalance, $optype) = $this->get_op_balance($row->id);
		
			// 'ledgers' table a 'op_balance_dc' field ti jodi "D" hoi
			if ($optype == "D")
			{
				// $total = $total_op + $opbalance
				// total = 0 + 4 ($total = 0 & jodi $opbalance = 4 hoi)
				$total_op = float_ops($total_op, $opbalance, '+');
			} else {
				// $total = $total_op - $opbalance
				// total = 0 - 4 ($total = 0 & jodi $opbalance = 4 hoi)
				$total_op = float_ops($total_op, $opbalance, '-');
			}
		}
		// total return korbe
		return $total_op;
	}

	/* Return debit total as positive value */
	// get_dr_total($ledger_id) te 'entry_items' table er 'amount' field er jogfol drtotal hisebe return korbe.
	// "ledger_id" ti na thakle 0 return kore. 
	function get_dr_total($ledger_id)
	{
		// jodi ledger_id = 5 hoi,
		// "SELECT SUM(amount) AS drtotal FROM entry_items JOIN entries WHERE entries.id = entry_items.entry_id AND 'entry_items.ledger_id' = 5 AND entry_items.dc = D" statement ti generate korbe
		$this->db->select_sum('amount', 'drtotal')->from('entry_items')->join('entries', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->where('entry_items.dc', 'D');
		// sql ti RUN korbe
		$dr_total_q = $this->db->get();

		if ($dr_total = $dr_total_q->row())
			// amount er jogfol drtotal hisebe return korbe
			return $dr_total->drtotal;
		else
			// 'ledger_id' ti 'entry_items' a jodi na thake
			// tahole default value 0 return korbe
			return 0;
	}

	/* Return credit total as positive value */
	// get_dr_total($ledger_id) te 'entry_items' table er 'amount' field er jogfol drtotal hisebe return korbe.
	// "ledger_id" ti na thakle 0 return kore. 
	function get_cr_total($ledger_id)
	{
		// jodi ledger_id = 5 hoi,
		// "SELECT SUM(amount) AS crtotal FROM entry_items JOIN entries WHERE entries.id = entry_items.entry_id AND 'entry_items.ledger_id' = 5 AND entry_items.dc = C" statement ti generate korbe
		$this->db->select_sum('amount', 'crtotal')->from('entry_items')->join('entries', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->where('entry_items.dc', 'C');
		// sql ti RUN korbe
		$cr_total_q = $this->db->get();

		if ($cr_total = $cr_total_q->row())
			// amount er jogfol crtotal hisebe return korbe
			return $cr_total->crtotal;
		else
			// 'ledger_id' ti 'entry_items' a jodi na thake
			// tahole default value 0 return korbe
			return 0;
	}

	/* Delete reconciliation entries for a Ledger account */
	// "entry_items" table er 'reconciliation_date' field ti NULL kore dibe
	function delete_reconciliation($ledger_id)
	{
		$update_data = array(
			'reconciliation_date' => NULL,
		);
		// jodi "ledger_id" = 5 hoi, "UPDATE entry_items SET reconciliation_date = NULL WHERE ledger_id = 5" statement ti generate korbe
		$this->db->where('ledger_id', $ledger_id)->update('entry_items', $update_data);
		// sql ti update seshe return korbe
		return;
	}
}
