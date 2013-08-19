<?php

class Entry_model extends Model {

	function Entry_model()
	{
		parent::Model();
	}

	// "entries" table theke next_entry_number ti return korbe
	function next_entry_number($entry_type_id)
	{
		// jodi entry_type_id = 5 dhori, tahole "SELECT MAX(number) AS lastno FROM entries WHERE entry_type = 5" statement ti generate korbe
		$this->db->select_max('number', 'lastno')->from('entries')->where('entry_type', $entry_type_id);
		// SQL ti run korbe
		$last_no_q = $this->db->get();
		// kono row exist kina
		if ($row = $last_no_q->row())
		{
			// row exist hole lastno ti store korbe
			$last_no = (int)$row->lastno;
			// ekti increment hobe
			$last_no++;
			// increment hoyar por number ti return korbe
			return $last_no;
		} else {
			// row exist na hole 1 return korbe
			return 1;
		}
	}

	// "entries" table theke entries guli return korbe
	function get_entry($entry_id, $entry_type_id)
	{
		// entry_id jodi 303 & entry_type_id jodi 4 hoi, tahole "SELECT * FROM entries WHERE id = 303 & entry_type = 4 LIMIT 1" statement ti generate kore
		$this->db->from('entries')->where('id', $entry_id)->where('entry_type', $entry_type_id)->limit(1);
		// SQL ti run
		$entry_q = $this->db->get();
		// data guli return korbe
		return $entry_q->row();
	}
}
