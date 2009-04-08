<?php

/**
  * MySQL Database Management Module
  *
  * <pre>
  * ----------------------- Revision History ---------------------------
  * v.2.6	- Updated to mysql_real_escape_string()
  * v.2.5	- Added replaceRow function to extend addRow
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
  * </pre>
  *
  * @package GloryLands
  * @subpackage Includes
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 2.5
  */


/**
  * @package GloryLands
  * @subpackage Includes
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
	 * The total time spent on queries
	 * @var float
	 */
	var $totTime;

	/**
	 * If we have global debug enabled, this function will store the
	 * queries being executed
	 * @var int
	 */
	var $queryList;


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
		
		// Initialize variables
		$queryList = array();
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
		$time=microtime(true);
		$result = mysql_query($text, $this->conID);
		$this->totQueries++;
		if (!$result) {
			if (defined("GLOB_DEBUG")) $this->queryList[]=array('query'=>$text,'result'=>false,'error'=>$this->getError());
			debug_error($this->getError(),ERR_WARNING);
			$this->emptyResults = true;
			$this->totTime+=(microtime(true)-$time);
			return false;
		}
		$this->lastResult = $result; 
		if ((substr(strtoupper($text), 0 ,6) == "SELECT") || (substr(strtoupper($text), 0 ,4) == "SHOW")) { 
			$this->numRows = mysql_num_rows($result);
			$this->emptyResults = ($this->numRows == 0);
			if (defined("GLOB_DEBUG")) $this->queryList[]=array('query'=>$text,'result'=>true,'rows'=>$this->numRows);
		} else { 
			$this->affectedRows = mysql_affected_rows();		
			$this->emptyResults = ($this->affectedRows == 0);
			if (defined("GLOB_DEBUG")) $this->queryList[]=array('query'=>$text,'result'=>true,'rows'=>$this->affectedRows);
		}
		$this->totTime+=(microtime(true)-$time);
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
	  * @param bool $formatted	(optional) TRUE If you want the result to be a pre-formatted HTML response
	  * @return string	An HTML-Formatted error description
	  */
	function getError($formatted=true) {
		if ($formatted) {
			return "<font face=Arial size=1 color=red>MySQL error while " . $this->errPosition . " : <font color=blue>" . mysql_error() . "</font></font>";
		} else {
			return mysql_error();
		}
	}

	/**
	  * Insert a new row on specified table
	  *
	  * @param string $table	The table name to add the data
	  * @param array $data		An one-dimensional array that contains the field names (as keys) and the field values to add
	  * @param bool $replace	If TRUE the import query will be built using REPLACE INTO instead of INSERT INTO
	  * @return bool|resource	Returns false in case of error or the resultset of the executed query
	  */
	function addRow($table, $data) {
		$vars = ""; $vals = "";
		foreach ($data as $name => $value) {
			if ($vars != "") $vars .= ", ";
			if ($vals != "") $vals .= ", ";
			
			$vars .= "`{$name}`";
			$vals .= "'".mysql_real_escape_string($value)."'";
		}
		
		return $this->query("INSERT INTO `{$table}` ({$vars}) VALUES ({$vals})");
	}

	/**
	  * Replace or insert a new row on specified table
	  *
	  * @param string $table	The table name to add the data
	  * @param array $data		An one-dimensional array that contains the field names (as keys) and the field values to add
	  * @return bool|resource	Returns false in case of error or the resultset of the executed query
	  */
	function replaceRow($table, $data) {
		$vars = ""; $vals = "";
		foreach ($data as $name => $value) {
			if ($vars != "") $vars .= ", ";
			if ($vals != "") $vals .= ", ";
			
			$vars .= "`{$name}`";
			$vals .= "'".mysql_real_escape_string($value)."'";
		}
		
		return $this->query("REPLACE INTO `{$table}` ({$vars}) VALUES ({$vals})");
	}

	/**
	  * Performs a query and returns true if the results are not empty
	  *
	  * @param string $query	The query to execute
	  * @return bool			Returns false in case of error or empty resultset, or true otherways
	  */
	function poll($query) {
	    $this->errPosition = "performing polling query '<strong>{$query}</strong>'";
		$time=microtime(true);
		$result = mysql_query($query, $this->conID);
		$this->totQueries++;
		if (!$result) {
			if (defined("GLOB_DEBUG")) $this->queryList[]=array('query'=>$query,'result'=>false,'error'=>$this->getError());
			debug_error($this->getError(),ERR_WARNING);
			$this->totTime+=(microtime(true)-$time);
			return false;
		} else {
			if (defined("GLOB_DEBUG")) $this->queryList[]=array('query'=>$query,'result'=>true,'rows'=>mysql_num_rows($result));
		}
		$ans = (mysql_num_rows($result)!=0);
		mysql_free_result($result);
		$this->totTime+=(microtime(true)-$time);
		return $ans;
	}
	
	/**
	  * Performs a query and returns the first value of the first row or false in case of error
	  *
	  * @param string $query	The query to execute
	  * @return bool|string		Returns false in case of error or the first row's first field value
	  */
	function query_and_get_value($query) {
	    $this->errPosition = "performing get value query '<strong>".htmlspecialchars($query)."</strong>'";
		$time=microtime(true);
		$result = mysql_query($query, $this->conID);
		$this->totQueries++;
		if (!$result) {
			if (defined("GLOB_DEBUG")) $this->queryList[]=array('query'=>$query,'result'=>false,'error'=>$this->getError());
			debug_error($this->getError(),ERR_WARNING);
			$this->totTime+=(microtime(true)-$time);
			return false;
		} else {
			if (defined("GLOB_DEBUG")) $this->queryList[]=array('query'=>$query,'result'=>true,'rows'=>mysql_num_rows($result));
		}
		if (mysql_num_rows($result)==0) return "";
		$row=mysql_fetch_array($result, MYSQL_NUM);
		mysql_free_result($result);
		$this->totTime+=(microtime(true)-$time);
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
			$q .= "`{$name}` = '".mysql_real_escape_string($value)."'";
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

	/**
	  * Visualize the queries being executed for debug purposes
	  *
	  * @return string	Returns an HTML formatted result with the queries and their status
	  */
	function getQueries() {
		$ans = '<table border="1" width="100%">';
		foreach ($this->queryList as $query) {
			$ans .= '<tr><td>'.$query['query'].'</td>';
			if ($query['result']) {
				$ans .= '<td><font color="green">OK</font></td>';
				$ans .= '<td>Returned/Affected '.$query['rows'].' rows</td>';
			} else {
				$ans .= '<td colspan="2">'.$query['error'].'</td>';
			}
			$ans .= '</tr>';
		}
		$ans .= '</table>';
		return $ans;
	}
	 
}

?>