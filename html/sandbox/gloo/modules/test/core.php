<?php

class GLUICore extends GLOOObject {

	function msgbox($text) {
		$this->_call('msgbox', $text);
	}

}

class GLUIPanel extends GLOOObject {

	function position($new_pos) {
		$this->left = $new_pos['x'];
		$this->top = $new_pos['y'];		
	}

}

?>