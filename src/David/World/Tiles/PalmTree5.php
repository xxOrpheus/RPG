<?php
namespace David\World\Tiles;
use David\World\Tile;

class PalmTree5 extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tileb.png');
		$this->pos(416, 32);
		$this->setClipped(true);
	}
}