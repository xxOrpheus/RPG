<?php
namespace David\World\Tiles;
use David\World\Tile;

class Tree1 extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tile8.png');
		$this->pos(-247, -32);
		$this->dimensions(110, 150);
		$this->setClipped(true);
	}	
}