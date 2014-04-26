<?php
namespace David\World\Tiles;
use David\World\Tile;

class WoodFloor extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tilea5.png');
		$this->pos(32, 128);	
	}
}