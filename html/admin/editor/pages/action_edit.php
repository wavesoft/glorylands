<?php 
access_check(ACCESS_EDITOR); 

$data = "&lt;?php\n\n?&gt;";
if ($_REQUEST['file']) {

	// Update file
	if ($_REQUEST['action'] == 'save') {
		$manifest = $_REQUEST['manifest'];
		//echo "<pre>".print_r($manifest,true)."</pre>";
	}

	// And load it anyways
	$manifest = include(DIROF("ACTION.MANIFEST").$_REQUEST['file'].'.php');
	$data = file_get_contents(DIROF("ACTION").$_REQUEST['file'].'.php');
}

?>
<form method="post" action="">
<input type="hidden" name="action" value="save" />
<table class="editor" width="100%">
	<tr class="info">
    	<td>Action script:</td>
        <td>Run-time configuration:</td>
    </tr>
	<tr>
    	<td><textarea id="data" class="codepress php" style="width:100%;height:500px;" wrap="off" name="data"><?php echo $data; ?></textarea></td>
        <td width="350" valign="top">
        <table>
       	    <tr>
            	<td align="right">Default Output Processor:</td>
                <td><select name="manifest[default_outmode]">
                <option value="" <?php if($manifest['default_outmode']=='')echo('selected="selected"'); ?>>(None)</option>
                <?php
				$d = dir(DIROF("OUTPUT.PROCESSOR"));
				$buf_op = "";
				while (false !== ($entry = $d->read())) {
				   if (strtolower(substr($entry,-4))=='.php') {
					$file = substr($entry,0,-4);
					$file = substr($file,4);
					$sel = ($manifest['default_outmode']!=$file)?'':'selected="selected"';
					echo("<option value=\"$file\" $sel>$file</option>\n");
				   }
				}
				$d->close();
                ?>
                </select></td>                
            </tr>
       	    <tr>
            	<td align="right">Overriden Output Processor:</td>
                <td><select name="manifest[forced_outmode]">
                <option value="" <?php if($manifest['forced_outmode']=='')echo('selected="selected"'); ?>>(None)</option>
                <?php
				$d = dir(DIROF("OUTPUT.PROCESSOR"));
				$buf_op = "";
				while (false !== ($entry = $d->read())) {
				   if (strtolower(substr($entry,-4))=='.php') {
					$file = substr($entry,0,-4);
					$file = substr($file,4);
					$sel = ($manifest['forced_outmode']!=$file)?'':'selected="selected"';
					echo("<option value=\"$file\" $sel>$file</option>\n");
				   }
				}
				$d->close();
                ?>                
                </select></td>
            </tr>
       	    <tr>
            	<td align="right">Post Processor:</td>
                <td><select name="manifest[post_processor]">
                <option value="" <?php if($manifest['post_processor']=='')echo('selected="selected"'); ?>>(None)</option>
                <?php
				$d = dir(DIROF("OUTPUT.PROCESSOR"));
				$buf_op = "";
				while (false !== ($entry = $d->read())) {
				   if (strtolower(substr($entry,-4))=='.php') {
					$file = substr($entry,0,-4);
					$file = substr($file,4);
					$sel = ($manifest['post_processor']!=$file)?'':'selected="selected"';
					echo("<option value=\"$file\" $sel>$file</option>\n");
				   }
				}
				$d->close();
                ?>           
                </select></td>
            </tr>
       	    <tr>
            	<td align="right">Default Interface:</td>
                <td><select name="manifest[default_interface]">
                <option value="">(None)</option>
                <?php
				$d = dir(DIROF("DATA.INTERFACE"));
				$buf_iface = "";
				while (false !== ($entry = $d->read())) {
				   if (strtolower(substr($entry,-4))=='.tpl') {
					$file = substr($entry,0,-4);
					$sel = ($manifest['default_interface']!=$file)?'':'selected="selected"';
					echo("<option value=\"$file\" $sel>$file</option>\n");
				   }
				}
				$d->close();
				?>
                </select></td>
            </tr>
       	    <tr>
            	<td align="right">Overriden Interface:</td>
                <td><select name="manifest[forced_interface]">
                <option value="">(None)</option>
                <?php
				$d = dir(DIROF("DATA.INTERFACE"));
				$buf_iface = "";
				while (false !== ($entry = $d->read())) {
				   if (strtolower(substr($entry,-4))=='.tpl') {
					$file = substr($entry,0,-4);
					$sel = ($manifest['forced_interface']!=$file)?'':'selected="selected"';
					echo("<option value=\"$file\" $sel>$file</option>\n");
				   }
				}
				$d->close();
				?>
                </select></td>
            </tr>
       	    <tr>
            	<td align="right">Dependencies:</td>
                <td><select name="manifest[lib]" size="4" multiple="MULTIPLE">
                <option value="" selected="selected">(None)</option>
				<?php
                $d = dir(DIROF("ACTION.LIBRARY"));
                while (false !== ($entry = $d->read())) {
                   if (strtolower(substr($entry,-4))=='.php') {
                    $file = substr($entry,0,-4);
                    echo "<option value=\"$file\">$file</option>\n";
                   }
                }
                $d->close();
                ?>
                </select>
                </td>
            </tr>
       	    <tr>
            	<td align="right">Managers to load:</td>
                <td><select name="manifest[managers]" size="4" multiple="MULTIPLE">
               <option value="" selected="selected">(None)</option>
				<?php
                $d = dir(DIROF("SYSTEM.MANAGER"));
                while (false !== ($entry = $d->read())) {
                   if (strtolower(substr($entry,-4))=='.php') {
                    $file = substr($entry,0,-4);
                    echo "<option value=\"$file\">$file</option>\n";
                   }
                }
                $d->close();
                ?>
                </select>
                </td>
            </tr>
        </table>
        </td>
    </tr>
    <tr>
    	<td colspan="2"><input type="text" name="file" value="<?php echo $_REQUEST['file']; ?>" /> <input type="submit" value="Save" class="button" onchange="this.form.action.value='save';" /> <input type="submit" value="Load" class="button" nchange="this.form.action.value='load';"/></td>
    </tr>
</table>
</form>
<?php

?>