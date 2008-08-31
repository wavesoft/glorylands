<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//      File: Error hooking and handling functions
//                   _______
// _________________| TO DO |_________________
//  -
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

// Generate a preety table to display debug backtrace
function HTMLBacktrace($bt) {
	global $_CONFIG;
	global $err_last_id;
	if (!isset($err_last_id)) $err_last_id=0;
	$err_last_id++;
?>
<table border="0" width="100%" bgcolor="#E1E1FF" style="font-family:Arial, Helvetica, sans-serif; font-size:12px">
  <tr bgcolor="#333333">
  	<th width="16" style="color:#FFFFFF"><a href="javascript:void(0);" onclick="e=document.getElementById('expand_<?php echo $err_last_id; ?>');if(e.style.display==''){e.style.display='none'}else{e.style.display=''};"><img border="0" src="<?php echo $_CONFIG[GAME][REF_URL]; ?>/images/expand.gif" /></a></th>
  	<th style="color:#FFFFFF"><u>Debug Backtrace</u></th>
  </tr>
  <tr>
  	<td colspan="2">
	<table id="expand_<?php echo $err_last_id; ?>" border="0" width="100%" bgcolor="#E1E1FF" style="font-family:Arial, Helvetica, sans-serif; font-size:12px">
	  <tr bgcolor="#8080FF">
		<th width="32">&nbsp;</th>
		<th>Filename</th>
		<th>Function</th>
		<th>Arguments</th>
	  </tr>
<?php
	$i=0;
	foreach ($bt as $fn) {
?>
  <tr>
    <td><?php echo sizeof($bt)-$i; ?> &rArr;</td>
    <td><?php echo $fn['file']; ?>, [Line <i><?php echo $fn['line']; ?></i>]</td>
    <td><b><?php echo $fn['function']; ?>()</b></td>
    <td><pre style="overflow: auto;"><?php print_r($fn['args']); ?></pre></td>
  </tr>
<?php	
		$i++;
	}
?>
	</table>
	</td>
  </tr>
</table>
<?php
}

// Generate a preety table to display the debug bariable
function HTMLDebug($var) {
	global $_CONFIG;
	global $err_last_id;
	if (!isset($err_last_id)) $err_last_id=0;
	$err_last_id++;
?>
<table border="0" width="100%" bgcolor="#E1E1FF" style="font-family:Arial, Helvetica, sans-serif; font-size:12px">
  <tr bgcolor="#333333">
  	<th width="16" style="color:#FFFFFF"><a href="javascript:void(0);" onclick="e=document.getElementById('expand_<?php echo $err_last_id; ?>');if(e.style.display==''){e.style.display='none'}else{e.style.display=''};"><img border="0" src="<?php echo $_CONFIG[GAME][REF_URL]; ?>/images/expand.gif" /></a></th>
  	<th style="color:#FFFFFF"><u>Debug Information</u></th>
  </tr>
  <tr>
  	<td colspan="2">
	<table id="expand_<?php echo $err_last_id; ?>" border="0" width="100%" bgcolor="#E1E1FF" style="font-family:Arial, Helvetica, sans-serif; font-size:12px">
	  <tr>
		<td>
		<pre>
		<?php print_r($var); ?>
		</pre>
		</td>
	  </tr>
<?php
?>
	</table>
    </td>
  </tr>
</table>
<?php
}

// Global function to handle fatal errors (instead of die)
function fatalError($desc, $debuginfo = '', $debug = false) {
	global $_CONFIG;
?>
	<div style="background-color:#FFC6C6; border:1px dashed #FF5555; font-family:Arial, Helvetica, sans-serif; font-size:12px">
	<table>
		<tr>
			<td><img src="<?php echo $_CONFIG[GAME][REF_URL]; ?>/images/UI/msgbox-critical.gif" /></td>
			<td><b>FATAL</b> Error: <br /><b><?php echo $desc; ?></b>
<?php
	if (isset($_REQUEST['debug']) || $debug) {
?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
<?php		
		if ($debuginfo!='') HTMLDebug($debuginfo);
		HTMLBacktrace(debug_backtrace());
	}
	die();
?>	
		</td>
	</tr>
	</table>
	</div>
<?php
}

// Set The error handling function
function glErrorHandler($errno, $errstr, $errfile, $errline) {
    switch ($errno) {
    case E_USER_ERROR:
		@ob_end_clean();
        fatalError("System Error: [$errno] $errstr on $errfile line $errline");
        exit(1);
        break;

    case E_USER_WARNING:
		@ob_end_clean();
        fatalError("Warning: [$errno] $errstr on $errfile line $errline");
        break;

    case E_USER_NOTICE:
		@ob_end_clean();
        fatalError("Notice: [$errno] $errstr on $errfile line $errline");
        break;

    default:
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}

// set to the user defined error handler
$old_error_handler = set_error_handler("glErrorHandler");

?>
