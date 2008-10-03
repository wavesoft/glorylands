<?php

// Make sure we got all the data
if (!$_REQUEST['guid']) return;

// Notify the DynUpdate systen that a window is closed
gl_dynupdate_dispose($_REQUEST['guid']);

?>