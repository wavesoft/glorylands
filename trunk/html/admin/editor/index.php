<?php
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 

if (isset($_REQUEST['a'])) {
	if ($_REQUEST['a'] == 'login') {
		if (!gl_user_login($_REQUEST['username'], md5($_REQUEST['password']))) {
			echo "<div class=\"msg_error\">Username or password not valid!</div>";
		}
	} elseif ($_REQUEST['a'] == 'logout') {
		gl_user_logout();
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<link rel="stylesheet" type="text/css" href="theme/style.css">
<title>GloryLands :: Content Editor</title>
</head>
<?php
// If player is logged in, show the frameset
if (isset($_SESSION[PLAYER])) {
?>
<frameset rows="*" cols="200,*" framespacing="0" frameborder="no" border="0">
  <frame src="nav.php" name="left" scrolling="No" noresize="noresize" id="left" title="left" />
  <frame src="page.php" name="main" id="main" title="main" />
</frameset>
<noframes>
<body>
</body>
</noframes>
<?php
// If not, show the login screen
} else{ 
?>
<body>
<center>
<img src="theme/images/logo.png" />
<form method="post" action="">
<input type="hidden" name="a" value="login" />
<table class="login">
<tr>
	<td>Username:</td>
	<td><input type="text" name="username" value="" /></td>
</tr>
<tr>
	<td>Password:</td>
	<td><input type="password" name="password" value="" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td align="center"><button type="submit" >Login</button></td>
</tr>
</table>
</form>
</center>
</body>
<?php
}
?>
</html>
