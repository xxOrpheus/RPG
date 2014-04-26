<?php
namespace David\World\Tiles;
use David\World\Tile;

class LongGrass extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tilea2.png');
		$this->pos(-96, -96);
	}
}