<?php

class GLUICore extends GLOOObject {

	function msgbox($text) {
		$this->_call('msgbox', $text);
	}

}

?>