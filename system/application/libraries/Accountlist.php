<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accountlist
{
	var $id = 0;
	var $name = "";
	var $total = 0;
	var $optype = "";
	var $opbalance = 0;
	var $children_groups = array();
	var $children_ledgers = array();
	var $counter = 0;
	public static $temp_max = 0;
	public static $max_depth = 0;
	public static $csv_data = array();
	public static $csv_row = 0;

	function Accountlist()
	{
		// sorasori accountList class ti te land korle return kore deya hobe
		return;
	}

	// initialized
	function init($id)
	{
		$CI =& get_instance();
		// id = 0 kina check kora hocce
		// 0 hole class er object property jemon "id, name and total" er default value set korbe
		// 0 na hole ID er value diye "group" table theke data row ti retrive korbe
		if ($id == 0)
		{
			// object gulo ke default value diye rewrite kora hocce
			$this->id = 0;
			$this->name = "None";
			$this->total = 0;
		} else {
			// jodi ei class er id object er value 2 thake tahole "SELECT * FROM groups WHERE id=2 LIMIT 1" ei SQL ti generate kore
			$CI->db->from('groups')->where('id', $id)->limit(1);
			// SQL ti run kora holo
			$group_q = $CI->db->get();
			// SQL er data gulo ke object hisebe $group a store kora holo
			$group = $group_q->row();
			// class tir object gulo ke database er data diye rewrite kora hocce
			$this->id = $group->id;
			$this->name = $group->name;
			$this->total = 0;
		}
		// ei class er add_sub_ledgers mathod ti run kora holo
		$this->add_sub_ledgers();
		// ei class er add_sub_groups mathod ti run kora holo
		$this->add_sub_groups();
	}

	function add_sub_groups()
	{
		$CI =& get_instance();
		// jodi ei class er object ID = 2 hoi,
		// tahole "SELECT * FROM groups WHERE parent_id=2 LIMIT 1" ei SQL ti generate kore
		$CI->db->from('groups')->where('parent_id', $this->id);
		// SQL ti run kora holo
		$child_group_q = $CI->db->get();
		// data grouping korar jonno counter banano hocce
		$counter = 0;
		// SQL er data gulo ke array declare kore loop run kora holo
		foreach ($child_group_q->result() as $row)
		{
			// children_groups object tike array baniye ei class tir instance create kora holo
			$this->children_groups[$counter] = new Accountlist();
			// tarpor ini method ti run kora holo
			$this->children_groups[$counter]->init($row->id);
			// total er calculation kora holo
			// jodi total object tir value 0 hoi & children_groups object tir value 10 hoi
			// tahole calculation ti hobe, '$this->total = 0 + 10 = 10'
			// float_ops() er location: system/application/helpers/custom_helper.php
			$this->total = float_ops($this->total, $this->children_groups[$counter]->total, '+');
			// counter ti increment kora holo
			$counter++;
		}
	}

	function add_sub_ledgers()
	{
		$CI =& get_instance();
		// ladger_model ti load kora holo
		// model tir location: system/application/models/ledger_model.php
		$CI->load->model('Ledger_model');
		// jodi ei class er object ID = 2 hoi,
		// tahole "SELECT * FROM ledgers WHERE group_id=2" ei SQL ti generate kore
		$CI->db->from('ledgers')->where('group_id', $this->id);

		// bakituku add_sub_groups() er moto e kaj kore
		// ---------------------------------------------

		// SQL ti run kora holo
		$child_ledger_q = $CI->db->get();
		// counter er jonno bosano holo
		$counter = 0;
		// SQL er data gulo ke array declare kore loop run kora holo
		foreach ($child_ledger_q->result() as $row)
		{
			// children_groups object tike array baniye database er data gulo store kora hocce
			$this->children_ledgers[$counter]['id'] = $row->id;
			$this->children_ledgers[$counter]['name'] = $row->name;
			
			// 'ledger_model' er 'get_ledger_balance()' a details deya ace
			$this->children_ledgers[$counter]['total'] = $CI->Ledger_model->get_ledger_balance($row->id);
			// $this->children_ledgers er 'opbalance' & 'optype' key te 'get_op_balance()' er return kora value set korbe
			// 'ledger_model' er 'get_op_balance()' a details deya ace
			list ($this->children_ledgers[$counter]['opbalance'], $this->children_ledgers[$counter]['optype']) = $CI->Ledger_model->get_op_balance($row->id);
			// total er calculation kora holo
			// jodi total object tir value 0 hoi & children_groups object tir value 10 hoi
			// tahole calculation ti hobe, '$this->total = 0 + 10 = 10'
			// float_ops() er location: system/application/helpers/custom_helper.php
			$this->total = float_ops($this->total, $this->children_ledgers[$counter]['total'], '+');
			// counter ti increment kora holo
			$counter++;
		}
	}

	/* Display Account list in Balance sheet and Profit and Loss st */
	function account_st_short($c = 0)
	{
		// class er property "counter" er value ei method er param diye set korbe,
		// default 0 set korbe
		$this->counter = $c;

		// jodi class property "id" 0 na hoy
		if ($this->id != 0)
		{
			// table row open tag print korbe
			echo "<tr class=\"tr-group\">";
			// table cell open tag print korbe
			echo "<td class=\"td-group\">";
			// class er property "counter" er value onujayee "&nbsp;" / " " print korbe
			echo $this->print_space($this->counter);
			// class er property "name" print korbe
			echo "&nbsp;" .  $this->name;
			// table cell end tag print korbe
			echo "</td>";
			// table cell print korbe
			// convert_amount_dc() er details "system/application/helpers/custom_helper.php" a deya ace
			echo "<td align=\"right\">" . convert_amount_dc($this->total) . $this->print_space($this->counter) . "</td>";
			// table row end tag print korbe
			echo "</tr>";
		}
		// class er property "children_groups" er value guli ke extract korbe
		foreach ($this->children_groups as $id => $data)
		{
			// counter er ekti encrement korbe
			$this->counter++;
			// ei class er account_st_short() method ti dekhun
			$data->account_st_short($this->counter);
			// counter er ager value te fire asbe
			$this->counter--;
		}

		// class er property "children_ledgers" er songkha 0 er beshi hole
		if (count($this->children_ledgers) > 0)
		{
			// counter er ekti encrement korbe
			$this->counter++;

			// class er property "children_ledgers" er value guli ke extract korbe
			foreach ($this->children_ledgers as $id => $data)
			{
				// table row open tag print korbe
				echo "<tr class=\"tr-ledger\">";
				// table cell open tag print korbe
				echo "<td class=\"td-ledger\">";
				// class er property "counter" er value onujayee "&nbsp;" / " " print korbe
				echo $this->print_space($this->counter);
				// ledger statement er details page er link print korbe
				echo "&nbsp;" . anchor('report/ledgerst/' . $data['id'], $data['name'], array('title' => $data['name'] . ' Ledger Statement', 'style' => 'color:#000000'));
				// table cell close tag print korbe
				echo "</td>";
				// table cell print korbe
				// convert_amount_dc() er details "system/application/helpers/custom_helper.php" a deya ace
				echo "<td align=\"right\">" . convert_amount_dc($data['total']) . $this->print_space($this->counter) . "</td>";
				// table row end tag print korbe
				echo "</tr>";
			}
			// counter er ager value te fire asbe
			$this->counter--;
		}
	}

	/* Display chart of accounts view */
	function account_st_main($c = 0)
	{
		$this->counter = $c;
		if ($this->id != 0)
		{
			// table row open tag print korbe
			echo "<tr class=\"tr-group\">";
			// table cell open tag print korbe
			echo "<td class=\"td-group\">";
			// class er property "counter" er value onujayee "&nbsp;" / " " print korbe
			echo $this->print_space($this->counter);

			// class er property "id" jodi 5 er choto hoi, tahole class er property "name" er bold text print korbe
			if ($this->id <= 4)
				echo "&nbsp;<strong>" .  $this->name. "</strong>";

			// class er property "id" jodi 5 er boro hoi, tahole class er property "name" er normal text print korbe
			else
				echo "&nbsp;" .  $this->name;

			// table cell end tag print korbe
			echo "</td>";
			echo "<td>Group Account</td>";
			echo "<td>-</td>";
			echo "<td>-</td>";

			// class er property "id" jodi 5 er choto hoi, tahole ekti khali/blank cell print korbe
			if ($this->id <= 4)
			{
				// table cell open tag print korbe
				echo "<td class=\"td-actions\"></td>";
			} else {
				// group account er edit & delete page er link print korbe
				echo "<td class=\"td-actions\">" . anchor('group/edit/' . $this->id , "Edit", array('title' => 'Edit Group', 'class' => 'red-link'));
				echo " &nbsp;" . anchor('group/delete/' . $this->id, img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete group')), array('class' => "confirmClick", 'title' => "Delete Group")) . "</td>";
			}
			// table row end tag print korbe
			echo "</tr>";
		}
		// class er property "children_groups" er value guli ke extract korbe
		foreach ($this->children_groups as $id => $data)
		{
			// counter er ekti encrement korbe
			$this->counter++;
			$data->account_st_main($this->counter);
			// counter er ager value te fire asbe
			$this->counter--;
		}
		// class er property "children_ledgers" er songkha 0 er beshi hole
		if (count($this->children_ledgers) > 0)
		{
			// counter er ekti encrement korbe
			$this->counter++;
			// class er property "children_ledgers" er value guli ke extract korbe
			foreach ($this->children_ledgers as $id => $data)
			{
				// table row open tag print korbe
				echo "<tr class=\"tr-ledger\">";
				// table cell open tag print korbe
				echo "<td class=\"td-ledger\">";
				// class er property "counter" er value onujayee "&nbsp;" / " " print korbe
				echo $this->print_space($this->counter);
				// ledger statement er details page er link print korbe
				echo "&nbsp;" . anchor('report/ledgerst/' . $data['id'], $data['name'], array('title' => $data['name'] . ' Ledger Statement', 'style' => 'color:#000000'));
				// table cell end tag print korbe
				echo "</td>";
				// table cell open tag print korbe
				echo "<td>Ledger Account</td>";
				// table cell print korbe
				// convert_opening() er details "system/application/helpers/custom_helper.php" a deya ace
				echo "<td>" . convert_opening($data['opbalance'], $data['optype']) . "</td>";
				// table cell print korbe
				// convert_amount_dc() er details "system/application/helpers/custom_helper.php" a deya ace
				echo "<td>" . convert_amount_dc($data['total']) . "</td>";

				// ledger er edit & delete page er link print korbe
				echo "<td class=\"td-actions\">" . anchor('ledger/edit/' . $data['id'], 'Edit', array('title' => "Edit Ledger", 'class' => 'red-link'));
				echo " &nbsp;" . anchor('ledger/delete/' . $data['id'], img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Ledger')), array('class' => "confirmClick", 'title' => "Delete Ledger")) . "</td>";
				// table row end tag print korbe
				echo "</tr>";
			}
			// counter er ager value te fire asbe
			$this->counter--;
		}
	}

	// counter onujayee "&nbsp;" / " " return korbe
	function print_space($count)
	{
		$html = "";
		for ($i = 1; $i <= $count; $i++)
		{
			$html .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		return $html;
	}
	
	/* Build a array of groups and ledgers */
	function build_array()
	{
		// item er default value class er property er value gulo set korbe
		$item = array(
			'id' => $this->id,
			'name' => $this->name,
			'type' => "G",
			'total' => $this->total,
			'child_groups' => array(),
			'child_ledgers' => array(),
			'depth' => self::$temp_max,
		);
		$local_counter = 0;
		// class er property "children_groups" er value songkha jodi 0 er beshi hoi
		if (count($this->children_groups) > 0)
		{
			// class er property "temp_max" er value ekti encrement hobe
			self::$temp_max++;

			// class er property "temp_max" jodi "max_depth" er ceye boro hoi
			// tahole "max_depth" er value "temp_max" er soman kore dey
			if (self::$temp_max > self::$max_depth)
				self::$max_depth = self::$temp_max;

			// class er property "children_groups" er value guli ke extract korbe
			foreach ($this->children_groups as $id => $data)
			{
				// item er child_groups key te local_counter onujayee array build korbe
				$item['child_groups'][$local_counter] = $data->build_array();
				// local_counter ti increment kora holo
				$local_counter++;
			}
			// class er property "temp_max" er value ager value te fire asbe
			self::$temp_max--;
		}
		$local_counter = 0;
		// class er property "children_ledgers" er songkha 0 er beshi hole
		if (count($this->children_ledgers) > 0)
		{
			// class er property "temp_max" er value ekti encrement hobe
			self::$temp_max++;
			// class er property "children_ledgers" er value guli ke extract korbe
			foreach ($this->children_ledgers as $id => $data)
			{
				// item er child_ledgers key te arry build korbe 
				$item['child_ledgers'][$local_counter] = array(
					'id' => $data['id'],
					'name' => $data['name'],
					'type' => "L",
					'total' => $data['total'],
					'child_groups' => array(),
					'child_ledgers' => array(),
					'depth' => self::$temp_max,
				);
				// local_counter ti increment kora holo
				$local_counter++;
			}
			// class er property "temp_max" er value ager value te fire asbe
			self::$temp_max--;
		}
		// item array ti return korbe
		return $item;
	}

	/* Show array of groups and ledgers as created by build_array() method */
	// build_array() method er value gula print korbe
	function show_array($data)
	{
		// table row open tag print korbe
		echo "<tr>";
		// table cell open tag print korbe
		echo "<td>";
		// class er property "counter" er value onujayee "&nbsp;" / " " print korbe
		echo $this->print_space($data['depth']);
		echo $data['depth'] . "-";
		echo $data['id'];
		echo $data['name'];
		echo $data['type'];
		echo $data['total'];
		if ($data['child_ledgers'])
		{
			// 'child_ledgers' er value gula extract korbe & print korbe
			foreach ($data['child_ledgers'] as $id => $ledger_data)
			{
				$this->show_array($ledger_data);
			}
		}
		if ($data['child_groups'])
		{
			// 'child_groups' er value gula extract korbe & print korbe
			foreach ($data['child_groups'] as $id => $group_data)
			{
				$this->show_array($group_data);
			}
		}
		// table cell end tag print korbe
		echo "</td>";
		// table row end tag print korbe
		echo "</tr>";
	}

	// csv format a convert kora
	function to_csv($data)
	{
		$counter = 0;
		while ($counter < $data['depth'])
		{
			// param tir 'depth' onujayee class er property csv_data er key add hote thabe
			self::$csv_data[self::$csv_row][$counter] = "";
			// counter ti increment kora holo
			$counter++;
		}
		// param tir 'depth' er beshi "counter" key te param tir 'name' set kora holo
		self::$csv_data[self::$csv_row][$counter] = $data['name'];
		// counter ti increment kora holo
		$counter++;

		while ($counter < self::$max_depth + 3)
		{
			// counter ti class property 'max_depth' er 3 beshi hoya porjonto class er property csv_data er key add hote thabe
			self::$csv_data[self::$csv_row][$counter] = "";
			// counter ti increment kora holo
			$counter++;
		}
		// current "counter" key te param tir 'type' set kora holo
		self::$csv_data[self::$csv_row][$counter] = $data['type'];
		// counter ti increment kora holo
		$counter++;

		// jodi param tir 'total' = 0 hoi
		if ($data['total'] == 0)
		{
			// current "counter" key te blank data set korbe
			self::$csv_data[self::$csv_row][$counter] = "";
			// counter ti increment kora holo
			$counter++;
			// current "counter" key te blank data set korbe
			self::$csv_data[self::$csv_row][$counter] = "";
		} else if ($data['total'] < 0) {
			// current "counter" key te "Cr" set korbe
			self::$csv_data[self::$csv_row][$counter] = "Cr";
			// counter ti increment kora holo
			$counter++;
			// current "counter" key te param tir 'total' er nagetive value set korbe
			self::$csv_data[self::$csv_row][$counter] = -$data['total'];
		} else {
			// current "counter" key te "Dr" set korbe
			self::$csv_data[self::$csv_row][$counter] = "Dr";
			// counter ti increment kora holo
			$counter++;
			// current "counter" key te param tir 'total' er positive value set korbe
			self::$csv_data[self::$csv_row][$counter] = $data['total'];
		}

		if ($data['child_ledgers'])
		{
			// 'child_ledgers' er value gula extract korbe
			foreach ($data['child_ledgers'] as $id => $ledger_data)
			{
				// class er property "csv_row" er value ekti encrement hobe
				self::$csv_row++;

				// 'child_ledgers' er value gula diye ei method ti punoray run korbe
				$this->to_csv($ledger_data);
			}
		}
		if ($data['child_groups'])
		{
			// 'child_groups' er value gula extract korbe
			foreach ($data['child_groups'] as $id => $group_data)
			{
				// class er property "csv_row" er value ekti encrement hobe
				self::$csv_row++;
				// 'child_groups' er value gula diye ei method ti punoray run korbe
				$this->to_csv($group_data);
			}
		}
	}

	// class property 'csv_data' ti return korbe
	public static function get_csv()
	{
		return self::$csv_data;
	}
	
	// class property 'csv_row' ti khali/blank kore deya hoi
	public static function add_blank_csv()
	{
		// class er property "csv_row" er value ekti encrement hobe
		self::$csv_row++;
		// class property 'csv_row' ti khali/blank kore deya holo
		self::$csv_data[self::$csv_row] = array("", "");
		// class er property "csv_row" er value ekti encrement hobe
		self::$csv_row++;
		// class property 'csv_row' ti khali/blank kore deya holo
		self::$csv_data[self::$csv_row] = array("", "");
		return;
	}
	
	// csv data te notun ekta row add korbe
	public static function add_row_csv($row = array(""))
	{
		// class er property "csv_row" er value ekti encrement hobe
		self::$csv_row++;
		// class property 'csv_row' te notun row ti add kore deya holo
		self::$csv_data[self::$csv_row] = $row;
		return;
	}

	// class property 'max_depth' & 'temp_max' ke 0 kore deya hoi
	public static function reset_max_depth()
	{
		self::$max_depth = 0;
		self::$temp_max = 0;
	}

	/*
	 * Return a array of sub ledgers with the object
	 * Used in CF ledgers of type Assets and Liabilities
	*/
	function get_ledger_ids()
	{
		$ledgers = array();
		// class er property "children_ledgers" er songkha 0 er beshi hole
		if (count($this->children_ledgers) > 0)
		{
			// class er property "children_ledgers" er value guli ke extract korbe
			foreach ($this->children_ledgers as $id => $data)
			{
				// 'children_ledgers' er id gula notun ekta array te collect kora hocce
				$ledgers[] = $data['id'];
			}
		}
		// class er property "children_groups" er value songkha jodi 0 er beshi hoi
		if (count($this->children_groups) > 0)
		{
			// class er property "children_groups" er value guli ke extract korbe
			foreach ($this->children_groups as $id => $data)
			{
				// ei method theke joto guli id paoya value guli ke extract korbe
				foreach ($data->get_ledger_ids() as $row)
					// value gula ageer array te collect kora hocce
					$ledgers[] = $row;
			}
		}
		// collected data guli return kora holo
		return $ledgers;
	}
}

