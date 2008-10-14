<?php

// Find out server statistics
$users = $sql->query_and_get_value("SELECT count(*) FROM `users_accounts` WHERE `online` = 1");
$max_users = 100;
$perc = ceil(100*$users/$max_users);
$act_result['server_load_img'] = ceil(7*$perc/100);
$act_result['server_load_perc'] = $perc;

// Check what question to get
if (!isset($_REQUEST['question'])) {
	// Get the first question
	$ans = $sql->query("SELECT * FROM `data_regquiz_questions` ORDER BY `index` ASC LIMIT 0,1");
	if (!$ans) { $act_result['error'] = $sql->getError(); return; }
	if ($sql->emptyResults) { $act_result['error'] = 'No question data found!'; return; }
	$question = $sql->fetch_array(MYSQL_ASSOC);
} else {
	// Get the next question
	$ans = $sql->query("SELECT * FROM `data_regquiz_questions` WHERE `index` > ".$_REQUEST['question']." ORDER BY `index` ASC LIMIT 0,1");
	if (!$ans) { $act_result['error'] = $sql->getError(); return; }
	if ($sql->emptyResults) { 
		/* Complete operation */
		$act_result['error'] = 'Completed! Now fix me to do more :P'; 
		return; 
	}
	$row = $sql->fetch_array(MYSQL_NUM);
	$question = $sql->fetch_array(MYSQL_ASSOC);
}
$act_result['question'] = $question;

// Get the answers
$ans = $sql->query("SELECT * FROM `data_regquiz_answers` WHERE `question` = ".$question['index']);
if (!$ans) { $act_result['error'] = $sql->getError(); return; }
if ($sql->emptyResults) { $act_result['error'] = 'No question data found!'; return; }
$answers = $sql->fetch_array_all(MYSQL_ASSOC);
$act_result['answers'] = $answers;


?>