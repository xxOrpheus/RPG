<?php
namespace David\World\Tiles;
use David\World\Tile;

class TreeStump1 extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tile8.png');
		$this->pos(-120, -30);
		$this->setClipped(true);
	}	
}