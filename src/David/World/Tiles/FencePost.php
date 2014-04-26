<?php
namespace David\World\Tiles;
use David\World\Tile;

class FencePost extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tilea2.png');
		$this->pos(256, 0);
		$this->setClipped(true);
	}
}