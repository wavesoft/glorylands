<?php

// Inventory I/O Scheme
foreach ($act_result as $name => $value) {
	echo "&{$name}=".urlencode($value);
}

?>