<?php

// --------------------------------------------------------------------
//                  MySQL Database Management Module
//                   (C) 2007, John Haralampidis
//
// ----------------------- Revision History ---------------------------
// v.2.4	- Added getValue function to query and get a value
// v.2.3	- Added Script Run function
// v.2.2	- Added Poll function
// v.2.1	- Added mysql_escape_string() on edit and add
// v.2.0	- Structure modified to use Presistent connections
// v.1.4	- Introduced fetch_array_all, fetch_array_fromresults_all
// v.1.3	- Insert/Edit row functions added
// v.1.2    - Built-In UNIX to SQL time conversion
// v.1.1	- Introduced fetch_array_fromresults
// v.1.0    - First stable release
//

class db {

	var $conID;			// Connection ID Definition
	var $errPosition;	// Description of current action in case of error
	var $lastResult;	// Last query's results
	var $affectedRows;	// Last query's affected rows
	var $numRows;		// Last query's number of rows
	var $emptyResults;	// Last query didn't return any results
	var $totQueries; 	// The number of queries performed

	// INIT: Connect to MySQL and select database
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

	// Perform a query
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
	
	// Get a single value
	function get_value() {
		return $this->get_value_fromresults($this->lastResult);
	}
	function get_value_fromresults($result) {
		if (!$result) return false;
		$respond = mysql_fetch_array($this->lastResult, MYSQL_NUM); 
		return $respond[0];
	}

	
	// Free resultset
	function free_query($resultset) {
		mysql_free_result($resultset);
	}

	// Fetch an array with the results
	function fetch_array($resmode = MYSQL_BOTH) {
		if (!$this->lastResult) return false;
		return mysql_fetch_array($this->lastResult, $resmode); 
	}

	// Fetch a two-dimensional array with all the results
	function fetch_array_all($resmode = MYSQL_BOTH) {
		$res = array();
		while ($row = mysql_fetch_array($this->lastResult, $resmode)) {
			array_push($res, $row);
		}
		return $res;
	}

	// Fetch an array with the results specified by input
	function fetch_array_fromresults($resultset, $resmode = MYSQL_BOTH) {
		return mysql_fetch_array($resultset, $resmode); 
	}

	// Fetch a two-dimensional array with all the results specified by input
	function fetch_array_fromresults_all($resultset, $resmode = MYSQL_BOTH) {
		$res = array();
		while ($row = mysql_fetch_array($resultset, $resmode)) {
			array_push($res, $row);
		}
		return $res;
	}
	
	// Free resultset
	function free_results() {
		if (!mysql_free_result($this->lastResult)) return false;
		return true;
	}
	
	// Navigate to specific row
	function gotorow($row) {
		if (!mysql_data_seek($this->lastResult, $row)) returnfalse;
		return true;
	}
	
	// Return last error
	function getError() {
		return "<font face=Arial size=1 color=red>MySQL error while " . $this->errPosition . " : <font color=blue>" . mysql_error() . "</font></font>";
	}

	// Insert a new row on specified table. The data are in an array format
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

	// Performs a query and returns true if the results are not empty
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
	
	// Return the first value of the queried string
	function getValue($query) {
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

	// Update a row on specified table, Indexed by the specified Where clause. The data are in an array format
	function editRow($table, $where, $data) {
		$q = "";
		foreach ($data as $name => $value) {
			if ($q != "") $q .= ", ";
			$q .= "`{$name}` = '".mysql_escape_string($value)."'";
		}
		return $this->query("UPDATE `{$table}` SET {$q} WHERE {$where}");
	}
	
	// Converts a UNIX timestamp into SQL timestamp
	function SQLTime($timestamp) {
		return date("YmdHis", $timestamp);		
	}

	// Converts an SQL timestamp into UNIX timestamp
	function UNIXTime($timestamp) {
		$y = substr($timestamp,0,4);
		$m = substr($timestamp,4,2);
		$d = substr($timestamp,6,2);
		$h = substr($timestamp,8,2);
		$i = substr($timestamp,10,2);
		$s = substr($timestamp,12,2);
		return mktime($h, $i, $s, $m, $d, $y);		
	}
	
	// Run SQL file
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