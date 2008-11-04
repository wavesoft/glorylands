<select style="width: 100%;" name="map">
<?php
$ans = $sql->query("SELECT `index`, `name` FROM `data_maps`");
while ($row = $sql->fetch_array(MYSQL_NUM)) {
	$selected='';
	if ($row[0]==$_REQUEST['quiz']) $selected='selected="selected"';
	echo "<option value=\"{$row[0]}\" {$selected}>{$row[1]}</option>\n";
}
?>
</select>