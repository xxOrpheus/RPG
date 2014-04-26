<?php 
namespace David\World;

class Tile {
	public $tileFile = 'resources/tiles/tilea2.png';
	public $posX = 0, $posY = 0;
	public $width = 32, $height = 32;
	public $clipped = false;

	public function tile($tile) {
		$this->tileFile = $tile;
	}

	public function pos($x, $y) {
		$this->posX = $x;
		$this->posY = $y;
	}

	public function dimensions($w, $h) {
		$this->width = $w;
		$this->height = $h;
	}

	public function setClipped($clipped) {
		$this->clipped = $clipped;
	}

	public function getClipped() {
		return $this->clipped;
	}
}