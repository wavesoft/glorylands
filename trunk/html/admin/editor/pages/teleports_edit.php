<?php 

// Grant access only to editors and above
access_check(ACCESS_EDITOR); 

// Get map information
if (isset($_REQUEST['map'])) {
	$sql->query("SELECT * FROM `data_maps` WHERE `index` = ".$_REQUEST['map']);
	$row = $sql->fetch_array();
} else {
	die("<div class=\"msg_error\">Map file not specified!</div>");
}

?>
<style>
.rect {
	width: 16px;
	height: 16px;
	border: solid 2px #FF0000;
	position: absolute;
}
.rectv {
	width: 16px;
	height: 16px;
	border: solid 2px #0000FF;
	position: absolute;
}
.rect a, .rectv a {
	width: 16px;
	height: 16px;
	display: block;
}
</style>
<div style="overflow: auto; width: 100%; height: 100%">
<div style="position: relative">
<img src="tools/rendermap.php?map=<?php echo $row['filename']; ?>&scale=16" />
<?php 
// Get target teleports
$ans=$sql->query("SELECT
	`data_maps_teleports`.`index`,
	`data_maps_teleports`.`x`,
	`data_maps_teleports`.`y`,
	`data_maps_teleports`.`to_x`,
	`data_maps_teleports`.`to_y`,
	`data_maps`.`name`
	FROM
	`data_maps_teleports`
	Inner Join `data_maps` ON `data_maps_teleports`.`to_map` = `data_maps`.`index`
	WHERE `map` = ".$_REQUEST['map']);

while ($row = $sql->fetch_array_fromresults($ans, MYSQL_ASSOC)) {
	$x = $row['x']*16;
	$y = $row['y']*16;
	$desc = "Navigates to: {$row['to_x']},{$row['to_y']} on {$row['name']}";
?>
<a name="p<?php echo $row['index']; ?>"><div class="rect" style="left: <?php echo $x; ?>px; top: <?php echo $y; ?>px;"><a href="?page=teleports_pedit&map=<?php echo $_REQUEST['map']; ?>&point=<?php echo $row['index']; ?>" title="<?php echo $desc; ?>">&nbsp;</a></div></a>
<?php
}

// Get receiver teleports
$ans=$sql->query("SELECT
	`data_maps_teleports`.`index`,
	`data_maps_teleports`.`x`,
	`data_maps_teleports`.`y`,
	`data_maps_teleports`.`to_x`,
	`data_maps_teleports`.`to_y`,
	`data_maps`.`name`
	FROM
	`data_maps_teleports`
	Inner Join `data_maps` ON `data_maps_teleports`.`map` = `data_maps`.`index`
	WHERE `to_map` = ".$_REQUEST['map']);

while ($row = $sql->fetch_array_fromresults($ans, MYSQL_ASSOC)) {
	$x = $row['to_x']*16;
	$y = $row['to_y']*16;
	$desc = "Arrives from: {$row['x']},{$row['y']} on {$row['name']}";
?>
<a name="p<?php echo $row['index']; ?>"><div class="rectv" style="left: <?php echo $x; ?>px; top: <?php echo $y; ?>px;"><a href="?page=teleports_pedit&map=<?php echo $_REQUEST['map']; ?>&point=<?php echo $row['index']; ?>" title="<?php echo $desc; ?>">&nbsp;</a></div></a>
<?php
}
?>
</div></div>