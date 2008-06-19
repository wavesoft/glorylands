<?php
ob_implicit_flush();
set_time_limit(0);
define("NOZIP",1);
include "../config/config.php";
include "../engine/includes/base.php";
$g_pcltar_lib_dir = $_CONFIG[GAME][BASE]."/admin/includes/lib";
include "includes/lib/pclzip.lib.php";
include "includes/lib/pcltar.lib.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Game Packages</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
pre {
	font-size: 12px;
	color: #333333;
	background-color: #E9E9E9;
	border: 1px dashed #CCCCCC;
	margin: 3px;
	padding: 3px;
}
table {
	border: 1px solid #999999;
}
-->
</style>
</head>
<body>
<?php
function cleanDir($base) {
	$d = dir($base);
	while (false !== ($entry = $d->read())) {
		if (substr($entry,0,1)=='.') {
			// skip '.', '..', and hidden files (linux)
		} elseif (is_dir($base."/".$entry)) {
			cleanDir($base."/".$entry);
			rmdir($base."/".$entry);
		} else {
			unlink($base."/".$entry);
		}
	}
	$d->close();
}

// Load plugin info
$sql->query("SELECT * FROM `system_packages` WHERE `index` = ".$_REQUEST['id']);
if ($sql->emptyResults) die("This plugin ID is invalid!</pre>\n");
$p_info = $sql->fetch_array();

// Do we have a backup file defined?
if (!isset($_REQUEST['file'])) {
	
	// Package storage array
	$pkg = array();
	
	// Find out how many backups do we have
	$d = dir($_CONFIG[GAME][BASE]."/admin/archive");
	while (false !== ($entry = $d->read())) {
		// Get only the packages we want
		if (substr($entry, 0, 32) == $p_info['guid']) {
			
			$parts = explode("_",$entry);		// 9356baf8041fbea43108847887609dbc_[Y-M-D-H-i-s.zip]
			$parts = explode(".",$parts[1]);	// [Y-M-D-H-i-s].zip
			$parts = explode("-",$parts[0]);	// Y, M, D, H, I, S

			$info = array(
						"date" => $parts[2].'/'.$parts[1].'/'.$parts[0].' '.$parts[3].':'.$parts[4].':'.$parts[5],
						"file" => $entry
						);
			array_push($pkg, $info);
		}
	}
	$d->close(); 
	?>
	<p>Available restore positions for package <b><?php echo $p_info['name']; ?></b>:</p>
	<form action="packages_restore.php" method="get" onsubmit="return window.confirm('Do you really want to revert to this position? This action will overwrite the package files and cannot be undoed!','Restore confirmation');">
	<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>"
	<table class="general">
	<tr>
		<th width="40" class="general" width="16"></th>
		<th width="150" class="general" width="60">Date Backed up</th>
		<th width="120" class="general" width="60">Plugin Version</th>
	</tr>
	<?php
	
	if (sizeof($pkg) != 0) {
		$i = 0;
		foreach ($pkg as $zip) {
		?>	
		<tr>
			<td width="16" align="center"><input type="radio" name="file" value="<?php echo $zip['file']; ?>" /> <img src="images/restorepoint.gif" alt="Revert" title="Revert file to this position" border="0" /></td>
			<td width="60"><?php echo $zip['date']; ?></td>
			<td width="60">v1.0</td>
		</tr>
		<?php
		}	
		?>
		</table><br />
		<input type="button" onclick="window.history.go(-1)" value="Back" />
		<input type="submit" value="Revert package" />
		</form>
		<?php
	} else {
		?>	
		<tr>
			<td colspan="3" width="16" align="center"><i>(There are no restore positions)</i></td>
		</tr>
		<?php
		echo "</table></form>\n<br><input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />";
	}
	return;
}


echo "<pre>";

echo "Initializing backup engine...";
cleanDir($_CONFIG[GAME][BASE]."/admin/cache");
echo "<font color=\"green\">done</font>\n";

echo "Extracting backup archive ".$_REQUEST['file']."...";
if (!file_exists($_CONFIG[GAME][BASE]."/admin/archive/".$_REQUEST['file'])) {
	echo "<font color=\"red\">filename missing</font>\n";
	echo "</pre></body></html>";
	return;
}

$procOK = true;

// [Step 1] Extract
$zip = new PclZip($_CONFIG[GAME][BASE]."/admin/archive/".$_REQUEST['file']);
$files=$zip->extract($_CONFIG[GAME][BASE]."/admin/cache");	
if (!$files) {
	$procOK = false;
	echo "<font color=\"red\">failed</font>\n";
} else {
	$procOK = true;
	echo "<font color=\"green\">done</font>\n";
}

// [Step 2] Open file
if ($procOK) {
	echo "Reading backup information...";
	$bf = fopen($_CONFIG[GAME][BASE]."/admin/cache/filemap.inf","r");
	if (!$bf) {
		$procOK = false;
		echo "<font color=\"red\">failed</font>\n";
	} else {
		$procOK = true;
		echo "<font color=\"green\">done</font>\n";
	}
}

// [Step 3] Perform restore
if ($procOK) {
	echo "\n";
	while (!feof($bf)) {
		$s = fgets($bf, 4096);
		if (!(!$s) && (trim($s)!='')) {
			$s = str_replace("\n","",$s);
			$s = str_replace("\r","",$s);

			$f = explode("=", $s);
	
			// Set source file path
			$s_name = $_CONFIG[GAME][BASE]."/admin/cache/".$f[0];
			$s_hash = md5_file($s_name);
	
			// Find out the real file path
			$f_name = $f[1];
			$f_name = str_ireplace('{$BASE}',$_CONFIG[GAME][BASE], $f_name);
			foreach ($_CONFIG[DIRS][ALIAS] as $key => $path) {
				if ($path!='') $f_name = str_ireplace('{$'.$key.'}', $path ,$f_name);
			}
	
			// Copy file
			echo "Reverting <b>$f_name</b>...";
			
			// Check for existing file
			if (file_exists($f_name)) {
				// Check if both files are the same
				echo "exists...";
				$b = md5_file($f_name);
				if ($b == $s_hash) {
					echo "<font color=\"#CC9900\">same</font>\n";
				} else {
					$u1 = str_replace($_CONFIG[GAME][BASE],"",$s_name);
					//echo "<img src=\"$u1\">";
					$ans=unlink($f_name);
					if (!$ans) {
						echo "<font color=\"red\">cannot update!</font>\n";
					} else {
						copy($s_name, $f_name);
						echo "<font color=\"green\">replaced</font>\n";
					}
				}
			} else {
				copy($s_name, $f_name);
				echo "<font color=\"green\">ok</font>\n";
			}
		}
	}
	fclose($bf);
}

?>
</body>
</html>