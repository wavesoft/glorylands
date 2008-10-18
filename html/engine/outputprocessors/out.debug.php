<html>
<head><title>..:: Debug Output ::..</title></head>
<body background="images/UI/background.jpg" text="#FFFFFF">
<table>
<tr>
<td><img src="images/inventory/Spell_Holy_SealOfWrath.jpg"></td>
<td><font face="Arial, Helvetica, sans-serif" size="3"><b>Wavesoft Debug Console</b></font><br>
    <font face="Arial, Helvetica, sans-serif" size="1" color="#CCCCCC"><b>A raw output and variable dump console for debugging the GloryLands game interface. Should not be used by the ones that cannot read it!</b></font></td>
</tr>
</table>
<hr>
<pre>
<?php

print_r($_REQUEST[arr]);

echo "<font color=\"#00FF00\">Valid:</font>\n<b>";
print_r($act_valid?'ow yees, yeees... :P':'No. I\'m sorry...');
if (!$act_valid) {
	echo "Validity was rejected in <font color=\"#FFFF00\">$act_invalid_position</font> check.";
}
echo "\n</b>\n<font color=\"#00FF00\">Debug Error Stack:</font>\n<b>";
echo debug_render_errors();
echo "\n</b>\n<font color=\"#00FF00\">Result:</font>\n<b>";
print_r($act_result);
echo "\n</b>\n<font color=\"#00FF00\">Interface:</font>\n<b>";
print_r($act_interface);
echo "\n</b>\n<font color=\"#00FF00\">Classes:</font>\n<b>";
print_r($act_classes);
echo "\n</b>\n<font color=\"#00FF00\">Operation:</font>\n<b>";
print_r($act_operation);
echo "\n</b>\n<font color=\"#00FF00\">Request data:</font>\n<b>";
print_r($_REQUEST);
echo "\n</b>\n<font color=\"#00FF00\">Session data:</font>\n<b>";
print_r($_SESSION);
echo "\n</b>\n<font color=\"#00FF00\">Chained messages:</font>\n<b>";
print_r(popMessages(MSG_INTERFACE));
echo "\n</b>\n<font color=\"#00FF00\">Included files:</font>\n<b>";
$ar = get_included_files();
asort($ar,SORT_STRING);
print_r($ar);

?>
</b>
</pre>
</body>
</html>
