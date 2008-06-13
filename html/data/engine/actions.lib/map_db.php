<?php

// #####################################################
// MapDB Structure is used to access mapDB files since 
// map information is not efficient enough to be stored
// in SQL due to large ammount of data that need to be 
// exchanged.
// #####################################################
//
// Version 0.3.12
//

/* MapDB Structure ---
// ---------------- */

define(MAPDB_DEBUG,false);

// Tuning vars
define(MAPDB_SZ_HEAD_W, 5);			## Map Width 				\
define(MAPDB_SZ_HEAD_H, 5);			## Map Height				|   Stored on
define(MAPDB_SZ_HEAD_NAME, 40); 	## Map Name					|     HEAD 
define(MAPDB_SZ_HEAD_BACK, 30);		## Map default background	/
define(MAPDB_SZ_LAYER1, 30);        ## Layer 1 Image
define(MAPDB_SZ_LAYER2, 30); 		## Layer 2 Image
define(MAPDB_SZ_LAYER3, 30);		## Layer 3 Image
define(MAPDB_SZ_NPCLAYER, 1);		## NPC layer position : 1 = Below L0, 2 = Below L1, 3 = Below L2, 4 = Topmost
define(MAPDB_SZ_HEIGHT, 5);			## Tile height (Needed for walk distance calculation
define(MAPDB_SZ_MATERIAL, 5);		## Material attenuation (Needed for walk distance calculation

define(MAPDB_ENTRY_SIZE, MAPDB_SZ_LAYER1+MAPDB_SZ_LAYER2+MAPDB_SZ_LAYER3+MAPDB_SZ_NPCLAYER+MAPDB_SZ_HEIGHT+MAPDB_SZ_MATERIAL);

// Read/Write access to mapDB entry (used by admin)
class RWmapDB {

	// Map header
	var $mapW;
	var $mapH;
	var $mapName;
	var $mapBack;

	// Internal
	var $newfile;
	var $fID;	
	var $readBase;
	
	// Initialize file and read map header
	function RWmapDB($fname) {
	
   /**/ if (MAPDB_DEBUG) echo "Oppening <b>$fname</b>...";
		$this->newFile = !file_exists($fname);
		$this->fID = fopen($fname, "ab+");
		if (!$this->fID) {
			return false;
   /**/		if (MAPDB_DEBUG) echo "error<br>";
		}
   /**/	if (MAPDB_DEBUG) echo "OK<br>\n";
		// Load Map Info from it's header
   /**/	if (MAPDB_DEBUG) echo "Loading...";
		$this->mapW = fread($this->fID,MAPDB_SZ_HEAD_W);
		if (!$this->mapW) return; // (No Data exists) 
		$this->mapW = trim($this->mapW);
		$this->mapH = trim(fread($this->fID,MAPDB_SZ_HEAD_H));
		$this->mapName = trim(fread($this->fID,MAPDB_SZ_HEAD_NAME));
		$this->mapBack = trim(fread($this->fID,MAPDB_SZ_HEAD_BACK));
   /**/	if (MAPDB_DEBUG) echo "OK<br>Map name is <b>".$this->mapName."</b><br>Map size is <b>".$this->mapW."x".$this->mapH."</b><br>\n";
		
		// Store the position after the header as base 
		// for future data reference
		$this->readBase = ftell($this->fID);
	}

	function saveHead() {
		rewind($this->fID);
		fwrite($this->fID, str_pad($this->mapW,MAPDB_SZ_HEAD_W) , MAPDB_SZ_HEAD_W);
		fwrite($this->fID, str_pad($this->mapH,MAPDB_SZ_HEAD_H), MAPDB_SZ_HEAD_H);
		fwrite($this->fID, str_pad($this->mapName,MAPDB_SZ_HEAD_NAME), MAPDB_SZ_HEAD_NAME);
		fwrite($this->fID, str_pad($this->mapBack,MAPDB_SZ_HEAD_BACK), MAPDB_SZ_HEAD_BACK);
	}

	## [[DEFUNCT]] :: Writting to file always appends text to the end ##
	// Put all the cell's info into the file.
	function setMap($info) {
		if ($x>$this->mapW) $x=$this->mapW;
		fseek($this->fID,$this->readBase);
		for ($x=0; $x<$this->mapW; $x++) {
			for ($y=0; $y<$this->mapH; $y++) {
				fwrite($this->fID, str_pad($info[$x][$y]['layer0'],MAPDB_SZ_LAYER1), MAPDB_SZ_LAYER1);
				fwrite($this->fID, str_pad($info[$x][$y]['layer1'],MAPDB_SZ_LAYER2), MAPDB_SZ_LAYER2);
				fwrite($this->fID, str_pad($info[$x][$y]['layer2'],MAPDB_SZ_LAYER3), MAPDB_SZ_LAYER3);
			}
		}
	}

	// Get all the cell's info from the file.
	function getMap() {
		fseek($this->fID,$this->readBase);
		$res = array();
		for ($x=0; $x<$this->mapW; $x++) {
			for ($y=0; $y<$this->mapH; $y++) {
				$res[$x][$y]['layers'] = array(
					"layer0" => trim(fread($this->fID,MAPDB_SZ_LAYER1)),
					"layer1" => trim(fread($this->fID,MAPDB_SZ_LAYER2)),
					"layer2" => trim(fread($this->fID,MAPDB_SZ_LAYER3)),
					 "npcpos" => trim(fread($this->fID,MAPDB_SZ_NPCLAYER)),
					 "altitude" => trim(fread($this->fID,MAPDB_SZ_HEIGHT)),
					 "material" => trim(fread($this->fID,MAPDB_SZ_MATERIAL))
					);
			}
		}
		return $res;
	}

	// Read a cell info from the file.
	// $x and $y are zero-based
	function getCell($y,$x) {
		$pos = (($y*$this->mapW)+$x)*MAPDB_ENTRY_SIZE + $this->readBase;
		fseek($this->fID,$pos);
		return array("layer0" => trim(fread($this->fID,MAPDB_SZ_LAYER1)),
					 "layer1" => trim(fread($this->fID,MAPDB_SZ_LAYER2)),
					 "layer2" => trim(fread($this->fID,MAPDB_SZ_LAYER3)),
					 "npcpos" => trim(fread($this->fID,MAPDB_SZ_NPCLAYER)),
					 "altitude" => trim(fread($this->fID,MAPDB_SZ_HEIGHT)),
					 "material" => trim(fread($this->fID,MAPDB_SZ_MATERIAL))
					);
	}


}

/*
$db = new RWmapDB('C:\map1.map');

if ($db->newFile) {
	$db->mapH = 50;
	$db->mapW = 100;
	$db->mapName = "Wavesoft Test Map";
	$db->mapBack = "back-0-0.gif";
	$db->saveHead();
}


for ($x=0; $x<$db->mapW; $x++) {
	for ($y=0; $y<$db->mapH; $y++) {
		$info[$x][$y]['layer1']='map-'.rand(0,50)."-".rand(0,50).".gif";
		$info[$x][$y]['layer2']='map-'.rand(0,50)."-".rand(0,50).".gif";
		$info[$x][$y]['layer3']='map-'.rand(0,50)."-".rand(0,50).".gif";
	}
}

echo "Storing... <br>\n<pre>";
print_r($info);
echo "</pre>";
$db->setMap($info);


$cell = $db->getCell(39,39);
echo "Cell on 4,2:<br>\n<pre>";
print_r($cell);
echo "</pre>";
*/
?>