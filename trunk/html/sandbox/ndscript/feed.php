<?php

include "../../config/config.php";
include "../../engine/includes/base.php";

$log = fopen("log.txt","w");
fwrite($log, "==[Started]==============\n");
fwrite($log, "Request:\n".print_r($_REQUEST,true));

function render($node) {
	global $log;
	fwrite($log, "Rendering Node: ".print_r($node,true));
	
	$ans = '';
	if ($node['tp'] == 'var') {
		if ($node['op']!='') {
			$ans='$'.$node['nm'].' '.$node['op'].' '.render($node['ch'][0]);
		} else {
			$ans='$'.$node['nm'];
		}
	} elseif ($node['tp'] == 'const') {
		$ans=$node['nm'];
	} elseif ($node['tp'] == 'unknown') {
		$ans='/* '.$node['nm'].' */';
	} elseif ($node['tp'] == 'object') {
		$var=render($node['ch'][0]);
		if (substr($var,0,1)=='$') $var=substr($var,1);
		$ans='$'.$node['nm'].'->'.$var;
	} elseif ($node['tp'] == 'function') {
		$ans=$node['nm'].'(';
		$parm='';
		foreach ($node['ch'] as $child) {
			if ($parm!='') $parm.=',';
			$parm.=render($child);
		}
		$ans.=$parm.')';
	}
	
	return $ans;
}

if ($_REQUEST['a'] == 'compile') {
	
	$dat = json_decode(stripslashes($_REQUEST['json']),true);
	
	$f = fopen("compiled.php","w");

	foreach ($dat['ch'] as $row) {
		$line = render($row);
		fwrite($f,$line.";\n");
	}
	fclose($f);
	echo "{}";
	
} else if ($_REQUEST['a'] == 'list') {

	$dat = json_decode(stripslashes($_REQUEST['json']),true);
	
	$list = get_defined_functions();
	$complete_list = array_merge($list['internal'], $list['user']);
	echo json_encode($complete_list);

} else if ($_REQUEST['a'] == 'syntax') {

	$v_func = array(
		'gl_make_guid'
	);
	$v_func_inf = array(
		array(
			'desc' => 'Create a new GUID based on it\'s elemental information',
			'return' => 'number',
			'args' => array(
				array('index', 'number', 'The incremental number'),
				array('instance', 'boolean', 'if TRUE the GUID will be refered to an object'),
				array('group', 'string,number', 'The GUID group ID or name')
			)
		)
	);
	
	$php_func = get_defined_functions();
	$php_func = $php_func['user'];
	foreach ($php_func as $func) {
		$v_func[]=$func;
		$v_func_inf[]=array(
			'desc' => 'System function',
			'returl' => 'boolean',
			'args' => array(
				array('Arg','unknown','Sample argument')
			)
		);
	}

	$v_var = array(
		'result'
	);
	$v_var_inf = array(
		array(
			'desc' => 'The result information that will be processed by the output processor',
			'type' => 'array',
			'args' => array(
				array('mode', 'string', 'The output mode'),
				array('title', 'string', 'The window title'),
				array('icon', 'string', 'The title icon')
			)
		)
	);
	
	$php_var = array_keys(get_defined_vars());
	foreach ($php_var as $vr) {
		$v_var[] = $vr;
		$v_var_inf[] = array(
			'desc' => 'Variable defined from PHP',
			'type' => 'unknown'
		);
	}

	$v_obj = array(
		'sql'
	);
	$v_obj_inf = array(
		array(
			'desc' => 'The reference to the SQL database',
			'func' => array(
				'poll'
			),
			'func_inf' => array(
				array(
					'desc' => 'Check if a query is valid',
					'args' => array(
						array('query','string','The SQL query')
					)
				)
			),
			'cvar' => array(
				'isEmpty'
			),
			'cvar_inf' => array(
				array(
					'desc' => 'Returns TRUE if function is empty',
					'type' => 'boolean'
				)
			)
		)
	);
	
	$php_obj = get_declared_classes();
	foreach ($php_obj as $obj) {
		$v_obj[] = $obj;
		
		$obj_func = get_class_methods($obj);
		$obj_func_inf = array();
		foreach ($obj_func as $ofun) {
			$obj_func_inf[] = array(
				'desc' => "$obj function $ofun",
				'args' => array(
					array('arg','unknown','Sample argument')
				)
			);
		}
		
		$obj_var = get_class_vars($obj);
		$obj_var_inf = array();
		foreach ($obj_var as $ovar) {
			$obj_var_inf[] = array(
				'desc' => "$obj variable $ovar",
				'type' => 'unknown'
			);
		}
		
		$v_obj_inf[] = array(
			'desc' => 'A PHP Declared class',
			'func' => $obj_func,
			'func_inf' => $obj_func_inf,
			'cvar' => $obj_var,
			'cvar_inf' => $obj_var_inf
		);
	}
	
	echo json_encode(array(
		'func' => $v_func,
		'func_inf' => $v_func_inf,
		'cvar' => $v_var,
		'cvar_inf' => $v_var_inf,
		'obj' => $v_obj,
		'obj_inf' => $v_obj_inf
	));

} else {
	echo "{}";
}

fwrite($log, "End.\n\n");

?>