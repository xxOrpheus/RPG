<?php
namespace David\World\Tiles;
use David\World\Tile;

class Bush1 extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tile8.png');
		$this->pos(-360, 0);
	}	
}