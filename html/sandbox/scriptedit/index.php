<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Dynamic Editor</title>
<script language="javascript" src="includes/edit_area/edit_area_full.js" type="text/javascript"></script>
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

	// initialisation
	editAreaLoader.init({
		id: "code"	// id of the textarea to transform		
		,start_highlight: true	// if start with highlight
		,allow_resize: "both"
		,allow_toggle: true
		,replace_tab_by_spaces: 4
		,language: "en"
		,syntax: "php"	
		,toolbar: "charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
		,browsers: "known"
		,plugins: "charmap"
	});	
	
</script>
</head>

<body>
<form action="" method="post" onsubmit="">
<textarea cols="120" rows="20" id="code" name="code">
<?php echo stripslashes($_POST['code']); ?>
</textarea> 
<input type="submit" />
</form>
<div id="msg"></div>
<div id="pos" style="position: absolute; z-index: 1000; background-color: #FF0000; width:5px; height:5px;">X</div>
</body>
</html>
