<?php

include "lib/gldataset.php";
include "lib/glDOM.php";
include "lib/gltemplate.php";
include "lib/gl.php";
include "lib/gloomodule.php";
include "lib/gloolink.php";
include "lib/gloobject.php";

// Initialize glroylands
GL::Initialize();

if (GL::$mode == GLREQ_API) {
	
	if (isset($_POST['id'])) {
		if ($_POST['id'] >= 0) {
			$obj = GLOOLink::get_object($_POST['id']);
			if (method_exists($obj, $_POST['f'])) {
	 			call_user_func_array(array($obj, $_POST['f']), $_POST['d']);
			} else {
				// #@# Store error message
			}
		}
	}
	
	$ui = GLOOLink::get_object(0);

	$panel1 = GLOOLink::get_object(1);	
	$panel2 = GLOOLink::get_object(2);
	
	$panel2->left = $panel1->left+200;
	$panel2->top = $panel1->top+20;
	
} else {

	// That's all :)
	$ui = GLOOLink::get_object(0);
	if (!$ui) $ui = new GLUICore();
	$ui->test = 'hello!';
	
	$panel = new GLUIPanel();
	$panel->title = 'Welcome to GloryLands Object Oriented API';
	$panel->text = 'The GLOO is a set of functions that allow real-time PHP and Javascript interaction, letting you concern ornly about the PHP-side of the script. The javascript DOM will automatically follow the PHP object hierectary and every PHP object can directly communicate with the Javascript itnerface.';
	$ui->welcome = $panel;
	
	$panel = new GLUIPanel();
	$panel->title = 'Another panel';
	$panel->text = '<a href="javascript:var v=new GLUIPanel({\'text\': \'This is a test\', \'title\':\'Static testing\'});">Click me!</a>';
	$panel->right = 20;
	$ui->second = $panel;

}

// Render glorylands
GL::Render();

// Finalize glorylands 
GL::Finalize();

?>