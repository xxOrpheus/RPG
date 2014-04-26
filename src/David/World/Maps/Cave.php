<?php
namespace David\World\Maps;
use David\Client;
use David\Game;
use David\World\Map;
use David\World\Tiles\CaveFloor;

class Cave extends Map {
	public function __construct() {
		$this->setName('Cave');
		for($x = 0; $x < 25; $x++) {
			for($y = 0; $y < 19; $y++) {
				$this->addTile(new CaveFloor(), $x, $y, 0);
			}
		}
	}
}