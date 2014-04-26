<?php
namespace David\World\Tiles;
use David\World\Tile;

class Flower1 extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tile8.png');
		$this->pos(-330, 0);
	}	
}