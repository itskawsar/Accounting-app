<?php

class Group_model extends Model {

	function Group_model()
	{
		parent::Model();
	}

	// database theke sokol GROUPS gulo return korbe
	function get_all_groups($id = NULL)
	{
		// options ke ARRAY hisebe initialized korbe
		$options = array();
		// id NULL kina check korce
		if ($id == NULL)
			// jodi NULL hoi, "SELECT * FROM groups WHERE id > 0 ORDER BY name ASC" statement ti generate korbe
			$this->db->from('groups')->where('id >', 0)->order_by('name', 'asc');
		else
			// jodi id = 303 hoi, "SELECT * FROM groups WHERE id > 0 and id != 303 ORDER BY name ASC" statement ti generate korbe
			$this->db->from('groups')->where('id >', 0)->where('id !=', $id)->order_by('name', 'asc');

		// sql ti RUN korbe
		$group_parent_q = $this->db->get();
		// data guli ke ARRAY te declare kore loop a bosano holo
		foreach ($group_parent_q->result() as $row)
		{
			// options er key hisebe table er id & value hisebe table er name field er data gulo store kora hocce
			$options[$row->id] = $row->name;
		}
		// options er data gulo ke RETURN kora holo
		return $options;
	}

	// ledger er group ti collect kora hocce
	function get_ledger_groups()
	{
		// options ke ARRAY hisebe initialized korbe
		$options = array();
		// "SELECT * FROM groups WHERE id > 4 ORDER BY name ASC" statement ti generate korbe
		$this->db->from('groups')->where('id >', 4)->order_by('name', 'asc');
		// sql ti RUN korbe
		$group_parent_q = $this->db->get();
		// data guli ke ARRAY te declare kore loop a bosano holo
		foreach ($group_parent_q->result() as $row)
		{
			// options er key hisebe table er id & value hisebe table er name field er data gulo store kora hocce
			$options[$row->id] = $row->name;
		}
		// options er data gulo ke RETURN kora holo
		return $options;
	}
}
