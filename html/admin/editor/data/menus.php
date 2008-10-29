<?php
global $MENU;
$MENU = array(
	'regquiz' => array(						/* The key must be the same with the 'page' */			
		'page' => 'regquiz',				/* The page defines the page to be loaded from pages/<page>.php */
		'icon' => 'db.png',					/* The menu icon */
		'text' => 'Registration Quiz',		/* The menu text */
		'customnav' => 'quizlist',			/* Any custom file (from navigators/<customnav>.php) to be appended on the top */
		'submenu' => array(					/* Submenus in the same form as this menu */
			'rquiz_editq' => array(
				'icon' => 'edit.png',
				'text' => 'Edit Question',
				'page' => 'rquiz_editq'
			),
			'rquiz_addq' => array(
				'icon' => 'edit_add.gif',
				'text' => 'Add Question',
				'page' => 'rquiz_addq'
			),
			'rquiz_addr' => array(
				'icon' => 'edit_add.gif',
				'text' => 'Add Response',
				'page' => 'rquiz_addr'
			),
			'rquiz_addd' => array(
				'icon' => 'edit_add.gif',
				'text' => 'Add Data',
				'page' => 'rquiz_addd'
			)
		)
	),
	'maps' => array(
		'page' => 'maps',
		'icon' => 'globe2.png',
		'text' => 'Map management'
	),
	'teleports' => array(
		'page' => 'teleports',
		'icon' => 'recur.png',
		'text' => 'Teleport Points'
	),
	'gobj' => array(
		'page' => 'gobj',
		'icon' => 'system.png',
		'text' => 'Game Objects',
		'rquiz_addd' => array(
			'icon' => 'edit_add.gif',
			'text' => 'Add Data',
			'page' => 'rquiz_addd'
		)
	)
);
?>