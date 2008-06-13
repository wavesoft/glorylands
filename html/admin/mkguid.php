<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Generate GUID</title>

</head>
<body>
<pre>
<?php

echo md5( rand(1,100000) . microtime() . date("YmdHis") . rand(1,100000) . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

?>
</pre>
<input type="button" onclick="window.location.reload()" value="Generate Another"/>
</body>
</html>
