<?php
## Here is the script that checks if the current action
## is valid to be executed under specific limitations, such as the:
## 1) Current Map position
## 2) Current interface
## 3) Action timeouts

// If no login information is set, allow access only to login interfaces
if (!isset($_SESSION[PLAYER])){
	$ans=false;
	if ($act_operation=='interface.entry') $ans=true;
	if ($act_operation=='interface.entry.register') $ans=true;
	if (!$ans) return false;
}

// If game information provided, allow access only on the login and create char interfaces
if (!isset($_SESSION[PLAYER][DATA])){
	$ans=false;
	if ($act_operation=='interface.entry') $ans=true;
	if ($act_operation=='interface.entry.register') $ans=true;
	if ($act_operation=='interface.entry.newchar') $ans=true;
	if ($act_operation=='regquiz.quiz') $ans=true;
	if (!$ans) return false;
}

// Check if we have an active fight
if (isset($_SESSION['fight'])) {

}

return true;
?>