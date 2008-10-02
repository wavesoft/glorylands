<?php

if (!isset($_REQUEST['guid'])) return;
if (!isset($_REQUEST['slot'])) return;

$sql->query("DELETE FROM `mod_quickbar_slots` WHERE `guid` = ".$_REQUEST['guid']." AND `slot` = ".$_REQUEST['slot']);

qb_update_view();

?>