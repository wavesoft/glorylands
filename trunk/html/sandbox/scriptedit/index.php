<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Dynamic Editor</title>
<script language="javascript" src="includes/codepress/codepress.js" type="text/javascript"></script>
<script language="javascript" src="../../includes/mootools-release-1.11.js"></script>
<script language="javascript">
function $trace(obj) {
	ans='';
	$each(obj, function(value, name) {
		if (ans!='') ans+=', ';
		ans+='['+name+'] = '+value;
	});	
	return ans;
}

$(window).addEvent('load', function(e){

	//var myCpWindow = document.getElementById('myCpWindow');
	window.alert(myCpWindow);
	var editor = myCpWindow.editor.getDocument();
	//window.alert(editor);
	handler = function(e) {
		var e = new Event(e);
		var p = myCpWindow.contentWindow.getSelection();
		var v = $(e.target).getPosition();
		var cp = myCpWindow.editor.getRangeAndCaret();
		var sz = $(e.target).getSize().size;
		$('msg').setHTML('Position: '+$trace(v)+' carret/Range: '+$trace(cp)+ ' size: '+$trace(sz)+'<br /> Anchor: '+p.anchorOffset+' Focus: '+p.focusOffset+' Range: '+p.rangeCount+'<br />P:'+$trace(p));
		$('pos').setStyles({
			'top': sz.y,
			'left': 42+(cp[1]*8)
		});
	};
	
	if(editor.attachEvent) editor.attachEvent('onkeyup',handler);
	else editor.addEventListener('keyup',handler,false);
	if(editor.attachEvent) editor.attachEvent('onkeydown',handler);
	else editor.addEventListener('keydown',handler,false);
	if(editor.attachEvent) editor.attachEvent('onmouseup',handler);
	else editor.addEventListener('mouseup',handler,false);


});
</script>
</head>

<body>
<form action="" method="post" onsubmit="$('code').value=myCpWindow.getCode()">
<input type="hidden" name="code" id="code" />
<textarea cols="120" rows="20" class="codepress javascript linenumbers-on" id="myCpWindow">
<?php echo stripslashes($_POST['code']); ?>
</textarea> 
<input type="submit" />
</form>
<div id="msg"></div>
<div id="pos" style="position: absolute; z-index: 1000; background-color: #FF0000; width:5px; height:5px;">X</div>
</body>
</html>
