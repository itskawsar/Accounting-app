<?php

class Setting_model extends Model {

	function Setting_model()
	{
		parent::Model();
	}

	// "settings" table theke 1st settings guli return korbe
	function get_current()
	{
		$this->db->from('settings')->where('id', 1);
		$account_q = $this->db->get();
		return $account_q->row();
	}
}
