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
	'tips' => array(
		'page' => 'tips',
		'icon' => 'help.png',
		'text' => 'Introduction Tips',
		'submenu' => array(
			'tips_add' => array(
				'icon' => 'edit_add.gif',
				'text' => 'New Tip',
				'page' => 'tips_add'
			)
		)
	),
	'teleports' => array(
		'page' => 'teleports',
		'icon' => 'recur.png',
		'text' => 'Teleport Points',
		'customnav' => 'teleports',
		'submenu' => array(
			'teleports_edit' => array(
				'icon' => 'edit.png',
				'text' => 'Edit Map Teleports',
				'page' => 'teleports_edit'
			),
			'teleports_add' => array(
				'icon' => 'edit_add.gif',
				'text' => 'Add Teleport',
				'page' => 'teleports_add'
			)
		)
	),
	'gobj' => array(
		'page' => 'gobj',
		'icon' => 'system.png',
		'text' => 'Game Objects',
		'submenu' => array(
			'gobj_items' => array(
				'icon' => 'edit.png',
				'text' => 'Items',
				'page' => 'gobj_items'
			),
			'gobj_chars' => array(
				'icon' => 'edit.png',
				'text' => 'Characters',
				'page' => 'gobj_chars'
			),
			'gobj_gameobjecs' => array(
				'icon' => 'edit.png',
				'text' => 'Map Objects',
				'page' => 'gobj_gameobjecs'
			),
			'gobj_npc' => array(
				'icon' => 'edit.png',
				'text' => 'Non Playable Chars',
				'page' => 'gobj_npc'
			)
		)
	)
);
?>