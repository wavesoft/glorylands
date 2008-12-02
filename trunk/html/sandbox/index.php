<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands Rendering System v2.0</title>
<style>
#datapane {
	position: absolute; 
	left:0px; 
	top: 0px; 
}

#dataloader {
	position: absolute; 
	left: 0px; 
	top: 0px; 
	z-index: 1000000; 
	background-color: #000000; 
	width: 768px; 
	height: 512px; 
	background-image: url(images/loading.gif); 
	background-repeat: no-repeat; 
	background-position: center;
}
#dataloader div {
	top: 280px;
	position: absolute;
	color: #CCCCCC;
	width: 768px;
	text-align: center;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
}

#datahost {
	background-color: #000000;
	position: relative; 
	width: 768px; 
	height: 512px; 
	display: block; 
	left:0px; 
	top: 0px; 
	overflow: hidden;
}

</style>
<script language="javascript" src="../includes/mootools-release-1.11.js"></script>
<script language="javascript" src="includes/glapi-2.1-src.js"></script>
</head>

<body>
	<div id="datahost">
		<div id="datapane">
		<!-- Datapane Contents -->	
		<div style="border: solid 2px #FF0000; z-index: 1000000; text-align:center; width: 32px; height:32px; position:absolute;" id="zp">&nbsp;</div>
		</div>
		<div id="dataloader"><div id="dataloader_text"></div></div>
	</div>
	<div>
	<a href="javascript:map_feed('mode=0');">Remove all</a> | 
	<a href="javascript:map_feed('mode=1');">Display 10 random objects</a> | 
	<a href="javascript:map_feed('mode=2');">Fade effect</a> | 
	<a href="javascript:map_feed('mode=3');">Pop effect</a> | 
	<a href="javascript:map_feed('mode=4');">Zooom effect</a> | 
	<a href="javascript:map_feed('mode=5');">Drop effect</a> | 
	<a href="javascript:map_feed('mode=6');">Scroll moving</a> | 
	<a href="javascript:map_feed('mode=7');">Bounce moving</a> | 
	<a href="javascript:map_feed('mode=8');">Fade moving</a> | 
	</div>
</body>
</html>
