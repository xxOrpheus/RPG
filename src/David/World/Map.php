<?php
namespace David\World;

class Map {
	public $name = 'Map';
	public $tiles = array();
	public $clips = array();
	public $scriptTiles = array(array());

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function isClipped($x, $y) {
		$x = round($x / 32, 0, PHP_ROUND_HALF_DOWN);
		$y = round($y / 32, 0, PHP_ROUND_HALF_DOWN);
		$isClipped = in_array(array($x, $y), $this->clips);
		return $isClipped;
	}

	public function isScriptTile($x, $y) {
		$x = round($x / 32, 0, PHP_ROUND_HALF_DOWN);
		$y = round($y / 32, 0, PHP_ROUND_HALF_DOWN);
		$isScriptTile = isset($this->scriptTiles[$x][$y]) ? $this->scriptTiles[$x][$y] : false;
		return $isScriptTile;
	}

	public function addClip($x, $y) {
		$this->clips[] = array($x, $y);
	}

	public function addScriptTile($x, $y, \closure $script) {
		$this->scriptTiles[$x][$y] = $script;
	}

	public function addTile(Tile $tile, $x, $y, $z) {
		if(!is_int($x) || !is_int($y) || !is_int($z)) {
			return false;
		}
		if($tile->getClipped()) {
			$w = floor($tile->width / 32);
			$h = floor($tile->height / 32);
			for($i = $x; $i < $x + $w; $i++) {
				for($j = $y; $j < $y + $h; $j++) {
					$this->addClip($i, $j);
				}
			}
		}
		$this->tiles[] = array('x' => $x * 32, 'y' => ($y * 32), 'z' => $z, 'tile' => $tile);
		return true;
	}
}