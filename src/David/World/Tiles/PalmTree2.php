<?php
namespace David\World\Tiles;
use David\World\Tile;

class PalmTree2 extends Tile {
	public function __construct() {
		$this->tile('resources/tiles/tileb.png');
		$this->pos(384, 96);	
	}
}