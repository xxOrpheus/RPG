<?php
namespace David\World\Tiles;
use David\World\Tile;

class GrassHole extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tilea2.png');
		$this->pos(-128, 192);
	}	
}