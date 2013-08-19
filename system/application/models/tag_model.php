<?php

class Tag_model extends Model {

	function Tag_model()
	{
		parent::Model();
	}

	// "tags" table theke sob data return korbe
	// 'allow_none' enable hole "options" data er prothom key ti "(None)" set korbe
	function get_all_tags($allow_none = TRUE)
	{
		$options = array();
		if ($allow_none)
			$options[0] = "(None)";

		// "SELECT * FROM tags ORDER BY title ASC" statement ti generate korbe
		$this->db->from('tags')->order_by('title', 'asc');
		// sql ti run korbe
		$tag_q = $this->db->get();

		//query er data ke extract korbe & "options" array ti prepare korbe
		foreach ($tag_q->result() as $row)
		{
			$options[$row->id] = $row->title;
		}
		// "options" array ti return korbe
		return $options;
	}

	// "tags" table theke ekti tag return korbe
	// 'tag_id' 1 er ceye choto hole blank return korbe
	function show_entry_tag($tag_id)
	{
		// tag_id 1 er ceye choto hole blank return korbe
		if ($tag_id < 1)
			return "";

		// jodi tag_id = 30 hoi, "SELECT * FROM tags WHERE id > 30 LIMIT 1" statement ti generate korbe
		$this->db->from('tags')->where('id', $tag_id)->limit(1);
		// sql ti run korbe
		$tag_q = $this->db->get();

		//query er data ke extract korbe
		if ($tag = $tag_q->row())
			// jodi extract hoi, tahole tag ti print korar jonno html structure prepare kora hocce
			return "<span class=\"tags\" style=\"color:#" . $tag->color . "; background-color:#" . $tag->background . "\">" . $tag->title . "</span>";
		else
			// jodi extract na hoi, tahole blank data return korbe
			return "";
	}

	// "tags" table theke ekti tag link soho return korbe
	// 'tag_id' 1 er ceye choto hole blank return korbe
	function show_entry_tag_link($tag_id)
	{
		// 'tag_id' 1 er ceye choto hole blank return korbe
		if ($tag_id < 1)
			return "";

		// jodi tag_id = 30 hoi, "SELECT * FROM tags WHERE id > 30 LIMIT 1" statement ti generate korbe
		$this->db->from('tags')->where('id', $tag_id)->limit(1);
		// sql ti run korbe
		$tag_q = $this->db->get();

		//query er data ke extract korbe
		if ($tag = $tag_q->row())
			// jodi extract hoi, tahole tag ti print korar jonno link soho html structure prepare kora hocce
			return "<span class=\"tags\" style=\"color:#" . $tag->color . "; background-color:#" . $tag->background . "\">" . anchor("entry/show/tag/" . $tag->id , $tag->title, array('style' => 'text-decoration:none;color:#' . $tag->color . ';')) . "</span>";
		else
			// jodi extract na hoi, tahole blank data return korbe
			return "";
	}
	
	// "tags" table theke ekti tag name return korbe
	// 'tag_id' 1 er ceye choto hole blank return korbe
	function tag_name($tag_id)
	{
		// 'tag_id' 1 er ceye choto hole blank return korbe
		if ($tag_id < 1)
			return "";

		// jodi tag_id = 30 hoi, "SELECT * FROM tags WHERE id > 30 LIMIT 1" statement ti generate korbe
		$this->db->from('tags')->where('id', $tag_id)->limit(1);
		// sql ti run korbe
		$tag_q = $this->db->get();

		//query er data ke extract korbe
		if ($tag = $tag_q->row())
			// jodi extract hoi, tahole tag table er title field ti return korbe
			return $tag->title;
		else
			// jodi extract na hoi, tahole blank data return korbe
			return "";
	}
}
