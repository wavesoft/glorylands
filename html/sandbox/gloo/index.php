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

// That's all :)
$ui = GLOOLink::get_object(0);
if (!$ui) $ui = new GLUICore();
$ui->test = 'hello!';
$ui->push('child', new GLUICore());

// Render glorylands
GL::Render();

// Finalize glorylands 
GL::Finalize();

?>