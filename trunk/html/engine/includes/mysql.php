<?php

/**
  * MySQL Database Management Module
  *
  * ----------------------- Revision History ---------------------------
  * v.2.4	- Added query_and_get_value function to query and get a value
  * v.2.3	- Added Script Run function
  * v.2.2	- Added Poll function
  * v.2.1	- Added mysql_escape_string() on edit and add
  * v.2.0	- Structure modified to use Presistent connections
  * v.1.4	- Introduced fetch_array_all, fetch_array_fromresults_all
  * v.1.3	- Insert/Edit row functions added
  * v.1.2    - Built-In UNIX to SQL time conversion
  * v.1.1	- Introduced fetch_array_fromresults
  * v.1.0    - First stable release
  * --------------------------------------------------------------------
  *
  * @package GloryLands Engine
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 2.4
  */


/**
  * @package GloryLands Engine
  */
class db {

	/**
	 * Connection ID Definition
	 * @var resource
	 */
	var $conID;

	/**
	 * Description of current action in case of error
	 * @var string
	 */
	var $errPosition;

	/**
	 * Last query's results
	 * @var resource
	 */
	var $lastResult;

	/**
	 * Last query's affected rows
	 * @var int
	 */
	var $affectedRows;

	/**
	 * Last query's number of rows
	 * @var int
	 */
	var $numRows;

	/**
	 * Last query didn't return any results
	 * @var bool
	 */
	var $emptyResults;

	/**
	 * The number of queries performed
	 * @var int
	 */
	var $totQueries;


	/**
	  * Initializes MySQL Class
	  *
	  * @param string 	$vdb 		Database name
	  * @param string 	$vhost 		Database server host
	  * @param string 	$vuser 		Login user name
	  * @param string 	$vpwd 		Login user password
	  * @param bool 	$presistent Make a presitent connection with the server
	  */
	function db($vdb, $vhost, $vuser, $vpwd, $presistent = false) {
		// Connect to SQL
		$this->totQueries = 0;
		$this->errPosition = "connecting to MySQL";
		if ($presistent) {
			$id = mysql_pconnect($vhost, $vuser, $vpwd);
		} else {
			$id = mysql_connect($vhost, $vuser, $vpwd);
		}
		if (!$id) {
			return false;
		}
		$this->conID = $id;
		
		// Select database
		$this->errPosition = "selecting database '<strong>{$vdb}</strong>'";
		if (!mysql_select_db($vdb, $id)) return false;
		return true;
	}

	/**
	  * Performs a query
	  *
	  * @param string $text Query text to execute
	  * @return bool|resource Returns the query resultset or false in case of error
	  */
	function query($text) {
	    $this->errPosition = "performing query '<strong>{$text}</strong>'";
		$result = mysql_query($text, $this->conID);
		$this->totQueries++;
		if (defined("GLOB_DEBUG")) echo "<br><Font color=green>".$this->totQueries.") ".$text."</font><br>";
		if (!$result) {
			$this->emptyResults = true;
			return false;
		}
		$this->lastResult = $result; 
		if ((substr(strtoupper($text), 0 ,6) == "SELECT") || (substr(strtoupper($text), 0 ,4) == "SHOW")) { 
			$this->numRows = mysql_num_rows($result);
			$this->emptyResults = ($this->numRows == 0);
		} else { 
			$this->affectedRows = mysql_affected_rows();		
			$this->emptyResults = ($this->affectedRows == 0);
		}
		return $result;
	}	
	
	/**
	  * Return the first value of the first row of the requested query
	  *
	  * @return mixed|bool Returns the value or false in case of error
	  */
	function get_value() {
		return $this->get_value_fromresults($this->lastResult);
	}
	
	/**
	  * Return the first value of the first row of the requested query, using a specified resultset
	  *
	  * @param resource $result	Resultset obdained from query() function
	  * @return bool|resource Returns the value or false in case of error
	  */
	function get_value_fromresults($result) {
		if (!$result) return false;
		$respond = mysql_fetch_array($this->lastResult, MYSQL_NUM); 
		return $respond[0];
	}

	
	/**
	  * Release a specific query resultset
	  *
	  * @param resource $resultset	Resultset obdained from query() function
	  */
	function free_query($resultset) {
		mysql_free_result($resultset);
	}

	/**
	  * Return a row from the last queried resultset
	  *
	  * @param int $resmode	The type of array that is to be fetched. It's a constant and can take the following values: MYSQL_ASSOC, MYSQL_NUM, and the default value of MYSQL_BOTH
	  * @return bool|resource Returns the row or false in case of error or end of results
	  */
	function fetch_array($resmode = MYSQL_BOTH) {
		if (!$this->lastResult) return false;
		return mysql_fetch_array($this->lastResult, $resmode); 
	}

	/**
	  * Return all the rows from the last queried resultset
	  *
	  * @param int $resmode	The type of array that is to be fetched. It's a constant and can take the following values: MYSQL_ASSOC, MYSQL_NUM, and the default value of MYSQL_BOTH
	  * @return bool|resource Returns the row or false in case of error or end of results
	  */
	function fetch_array_all($resmode = MYSQL_BOTH) {
		$res = array();
		while ($row = mysql_fetch_array($this->lastResult, $resmode)) {
			array_push($res, $row);
		}
		return $res;
	}

	/**
	  * Return a row from a specific resultset
	  *
	  * @param resource $resultset	A resultset obdained by the query() function
	  * @param int $resmode			The type of array that is to be fetched. It's a constant and can take the following values: MYSQL_ASSOC, MYSQL_NUM, and the default value of MYSQL_BOTH
	  * @return bool|resource 		Returns the row or false in case of error or end of results
	  */
	function fetch_array_fromresults($resultset, $resmode = MYSQL_BOTH) {
		return mysql_fetch_array($resultset, $resmode); 
	}

	/**
	  * Return all the rows from a specific resultset
	  *
	  * @param resource $resultset	A resultset obdained by the query() function
	  * @param int $resmode			The type of array that is to be fetched. It's a constant and can take the following values: MYSQL_ASSOC, MYSQL_NUM, and the default value of MYSQL_BOTH
	  * @return bool|resource 		Returns the row or false in case of error or end of results
	  */
	function fetch_array_fromresults_all($resultset, $resmode = MYSQL_BOTH) {
		$res = array();
		while ($row = mysql_fetch_array($resultset, $resmode)) {
			array_push($res, $row);
		}
		return $res;
	}
	
	/**
	  * Release the last queried resultset
	  */
	function free_results() {
		if (!mysql_free_result($this->lastResult)) return false;
		return true;
	}
	
	/**
	  * Moves the internal row pointer to a new position
	  *
	  * @param int $row	The row index to jump to
	  */
	function gotorow($row) {
		if (!mysql_data_seek($this->lastResult, $row)) returnfalse;
		return true;
	}
	
	/**
	  * Returns detailed information for the last error occured
	  *
	  * @return string	An HTML-Formatted error description
	  */
	function getError() {
		return "<font face=Arial size=1 color=red>MySQL error while " . $this->errPosition . " : <font color=blue>" . mysql_error() . "</font></font>";
	}

	/**
	  * Insert a new row on specified table
	  *
	  * @param string $table	The table name to add the data
	  * @param array $data		An one-dimensional array that contains the field names (as keys) and the field values to add
	  * @return bool|resource	Returns false in case of error or the resultset of the executed query
	  */
	function addRow($table, $data) {
		$vars = ""; $vals = "";
		foreach ($data as $name => $value) {
			if ($vars != "") $vars .= ", ";
			if ($vals != "") $vals .= ", ";
			
			$vars .= "`{$name}`";
			$vals .= "'".mysql_escape_string($value)."'";
		}
		return $this->query("INSERT INTO `{$table}` ({$vars}) VALUES ({$vals})");
	}

	/**
	  * Performs a query and returns true if the results are not empty
	  *
	  * @param string $query	The query to execute
	  * @return bool|resource	Returns false in case of error or the resultset of the executed query
	  */
	function poll($query) {
	    $this->errPosition = "performing polling query '<strong>{$query}</strong>'";
		$result = mysql_query($query, $this->conID);
		$this->totQueries++;
		if (defined("GLOB_DEBUG")) echo "<br><Font color=green>".$this->totQueries.") ".$query."</font><br>";
		if (!$result) return false;
		$ans = (mysql_num_rows($result)!=0);
		mysql_free_result($ans);
		return $ans;
	}
	
	/**
	  * Performs a query and returns the first value of the first row or false in case of error
	  *
	  * @param string $query	The query to execute
	  * @return bool|string		Returns false in case of error or the first row's first field value
	  */
	function query_and_get_value($query) {
	    $this->errPosition = "performing get value query '<strong>{$query}</strong>'";
		$result = mysql_query($query, $this->conID);
		$this->totQueries++;
		if (defined("GLOB_DEBUG")) echo "<br><Font color=green>".$this->totQueries.") ".$query."</font><br>";
		if (!$result) return false;
		if (mysql_num_rows($result)==0) return "";
		$row=mysql_fetch_array($result, MYSQL_NUM);
		mysql_free_result($result);
		return $row[0];
	}

	/**
	  * Update a row on a table. The row to edit is defined by a where clause
	  *
	  * @param string $table	The table name from which to edit the data
	  * @param string $where	A MySQL WHERE-formatted query part. This is used to identify the item(s) to edit (ex. "`index` = 2")
	  * @param array $data		An one-dimensional array that contains the field names (as keys) and the field values to edit
	  * @return bool|resource	Returns false in case of error or the resultset of the executed query
	  */
	function editRow($table, $where, $data) {
		$q = "";
		foreach ($data as $name => $value) {
			if ($q != "") $q .= ", ";
			$q .= "`{$name}` = '".mysql_escape_string($value)."'";
		}
		return $this->query("UPDATE `{$table}` SET {$q} WHERE {$where}");
	}
	
	/**
	  * Converts a UNIX timestamp into SQL timestamp
	  *
	  * @param int $timestamp	A UNIX timestamp value
	  * @return string			Returns the value into MySQL timestamp format
	  */
	function SQLTime($timestamp) {
		return date("YmdHis", $timestamp);		
	}

	/**
	  * Converts a UNIX timestamp into SQL timestamp
	  *
	  * @param string $timestamp	A MySQL timestamp value
	  * @return int					Returns the value into UNIX timestamp format
	  */
	function UNIXTime($timestamp) {
		$y = substr($timestamp,0,4);
		$m = substr($timestamp,4,2);
		$d = substr($timestamp,6,2);
		$h = substr($timestamp,8,2);
		$i = substr($timestamp,10,2);
		$s = substr($timestamp,12,2);
		return mktime($h, $i, $s, $m, $d, $y);		
	}
	
	/**
	  * Execute a SQL script
	  *
	  * @param string $file		The filename to load and run
	  * @return bool			Returns true if all the queries were successfull or false if one query failed
	  */
	function run($file) {
	
		// Open file
		$f = fopen($file,"r");
		if (!$f) return;
		
		// Read file
		$incomment = false;
		$buffer = '';
		while (!feof($f)) {
			$row = trim(fgets($f, 4096));
			if ((substr($row,0,1)!='#') && (substr($row,0,2)!='--')) {
				if (substr($row,0,2) == '/*') $incomment = true;
				if (!$incomment) {
					$buffer .= $row."\n";
				}
				if (substr($row,-2) == '*/') $incomment = false;
			}
		}
		fclose($f);
		
		// Count queries
		$success = 0;
		
		// Break file queries
		$queries = explode(";\n", $buffer);
		foreach ($queries as $query) {
			if (trim($query)!='') {
				if (!$this->query($query)) return false;
			}
		}

		return true;
	}
}

?>