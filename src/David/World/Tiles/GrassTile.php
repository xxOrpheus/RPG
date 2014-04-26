<?php
namespace David\World\Tiles;
use David\World\Tile;

class GrassTile extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tilea2.png');
		$this->pos(0, 0);
	}
}