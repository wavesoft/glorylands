<?php

class mapobj {
	
	var $bindX, $bindY, $width, $height;
	var $grid;

	function mapobj($file = false) {
		
		// Do we have a file specified?
		if ($file!=false) {
		
			// Do we have a generated cache?
			if (file_exists($file.'bj')) {
				$data = unserialize(file_get_contents($file.'bj'));
				$this->grid=$data['grid'];
				$this->width=$data['w'];
				$this->height=$data['h'];
				$this->bindX=$data['x'];
				$this->bindY=$data['y'];
			} elseif (file_exists($file))  {
	
				$this->grid = array();
				$this->width=0;
				$lines = explode("\r\n",file_get_contents($file));
				$wh = explode(",",$lines[0]);
				unset($lines[0]);
				$this->bindX=$wh[0];
				$this->bindY=$wh[1];			
	
				$x=0; $y=0;
				foreach ($lines as $row) {
					if (trim($row)!='') {
						$cols = explode(",",$row);
						$x=0;
						foreach ($cols as $cell) {
							$this->grid[$x][$y] = $cell;
							$x++;
						}
						if ($x>$this->width) $this->width = $x;
						$y++;
					}
				}
				$this->height = $y;
			
				// And build cache
				$data = serialize(array(
					'grid'=>$this->grid,
					'w'=>(int)$this->width,
					'h'=>(int)$this->height,
					'x'=>(int)$this->bindX,
					'y'=>(int)$this->bindY
				));
				file_put_contents($file.'bj', $data);
			} else {
				$this->grid = array(0 => array(0 => 'blank.gif'));
				$this->width = 1;
				$this->height = 1;
				$this->bindX = 0;
				$this->bindY = 1;
			}
		}
	}
}

?>