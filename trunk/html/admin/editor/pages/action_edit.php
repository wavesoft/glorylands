<?php access_check(ACCESS_EDITOR); 
$d = dir(DIROF("OUTPUT.PROCESSOR"));
$buf_op = "";
while (false !== ($entry = $d->read())) {
   if (strtolower(substr($entry,-4))=='.php') {
	$file = substr($entry,0,-4);
	$file = substr($file,4);
	$buf_op .= "<option value=\"$file\">$file</option>\n";
   }
}
$d->close();

$d = dir(DIROF("DATA.INTERFACE"));
$buf_iface = "";
while (false !== ($entry = $d->read())) {
   if (strtolower(substr($entry,-4))=='.tpl') {
	$file = substr($entry,0,-4);
	$buf_iface .= "<option value=\"$file\">$file</option>\n";
   }
}
$d->close();

?>
<table class="editor">
	<tr class="info">
    	<td>Action script:</td>
        <td>Run-time configuration:</td>
    </tr>
	<tr>
    	<td><textarea id="cp1" class="codepress php" style="width:700px;height:300px;" wrap="off">&lt;?php

?&gt;</textarea></td>
        <td valign="top">
        <table>
       	    <tr>
            	<td align="right">Default Output Processor:</td>
                <td><select name="manifest[default_outmode]">
                <option value="">(None)</option>
                <?php echo $buf_op; ?>
                </select></td>                
            </tr>
       	    <tr>
            	<td align="right">Overriden Output Processor:</td>
                <td><select name="manifest[forced_outmode]">
                <option value="">(None)</option>
                <?php echo $buf_op; ?>
                </select></td>
            </tr>
       	    <tr>
            	<td align="right">Default Interface:</td>
                <td><select name="manifest[default_interface]">
                <option value="">(None)</option>
                <?php echo $buf_iface; ?>
                </select></td>
            </tr>
       	    <tr>
            	<td align="right">Overriden Interface:</td>
                <td><select name="manifest[forced_interface]">
                <option value="">(None)</option>
                <?php echo $buf_iface; ?>
                </select></td>
            </tr>
       	    <tr>
            	<td align="right">Post Processor:</td>
                <td><select name="manifest[post_processor]">
                <option value="">(None)</option>
                <?php echo $buf_op; ?>
                </select></td>
            </tr>
        </table>
        </td>
    </tr>
</table>

<?php

?>