<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * CSV Helpers
 * Inspiration from PHP Cookbook by David Sklar and Adam Trachtenberg
 * 
 * @author		Jérôme Jaglale
 * @link		http://maestric.com/en/doc/php/codeigniter_csv
 */

// ------------------------------------------------------------------------

/**
 * Array to CSV
 *
 * download == "" -> return CSV string
 * download == "toto.csv" -> download file toto.csv
 */
if ( ! function_exists('array_to_csv'))
{
	// array ke csv format a convert korbe
	// "download" blank hole return korbe
	// blank na hole download hobe
 	// jodi "download" = "toto.csv", tehole 'toto.csv' naam er file download hobe
	function array_to_csv($array, $download = "")
	{
		// "download" blank na hole header meta prepare korce
		if ($download != "")
		{	
			header('Content-Type: application/csv');
			header('Content-Disposition: attachement; filename="' . $download . '"');
		}		

		ob_start();
		// file open command diye various I/O streams er cccess neya holo
		// access na pele ekti error message show korbe
		$f = fopen('php://output', 'w') or show_error("Can't open php://output");
		$n = 0;
		// array tike extract kora hocce
		foreach ($array as $line)
		{
			$n++;
			// proti array er key ke notun ekta line a likha hocce
			// na likhte parle ekti error message show korbe
			if ( ! fputcsv($f, $line))
			{
				show_error("Can't write line $n: $line");
			}
		}
		// IO stream gula bondo kora hocce
		// bondo na korte parle error message show korbe
		fclose($f) or show_error("Can't close php://output");
		// proccessed data guli ke str variable a set kore ob stream ke clean kore deya holo
		$str = ob_get_contents();
		ob_end_clean();

		if ($download == "")
		{
			// "download" blank hole str variable ti return korbe
			return $str;	
		}
		else
		{	
			// "download" blank na hole download hobe
			echo $str;
		}		
	}
}

// ------------------------------------------------------------------------

/**
 * Query to CSV
 *
 * download == "" -> return CSV string
 * download == "toto.csv" -> download file toto.csv
 */
if ( ! function_exists('query_to_csv'))
{
	// query ke array te process kore, csv format a convert korbe
	// "download" blank hole return korbe
	// blank na hole download hobe
 	// jodi "download" = "toto.csv", tehole 'toto.csv' naam er file download hobe
	function query_to_csv($query, $headers = TRUE, $download = "")
	{
		// query ti object na hole
		// query object er "list_fields()" method ti exist na hole
		// invalid query error show korbe
		if ( ! is_object($query) OR ! method_exists($query, 'list_fields'))
		{
			show_error('invalid query');
		}
		
		$array = array();
		// jodi headers TRUE hoi,
		// query er list_fields() method ti theke value extract korbe
		if ($headers)
		{
			$line = array();
			foreach ($query->list_fields() as $name)
			{
				// field name guli ke line array te collect kora holo
				$line[] = $name;
			}
			// line array ti array er ekti notun key te store kora holo
			$array[] = $line;
		}
		
		// query er data gulike extract kora hocce
		foreach ($query->result_array() as $row)
		{
			$line = array();
			//protita row ke abar extract kora hocce
			foreach ($row as $item)
			{
				// protita single data ke line array te collect kora holo
				$line[] = $item;
			}
			// line array ti array er ekti notun key te store kora holo
			$array[] = $line;
		}
		// data gulike ei file er array_to_csv() method a pathano holo
		echo array_to_csv($array, $download);
	}
}

/* End of file csv_helper.php */
/* Location: ./system/helpers/csv_helper.php */