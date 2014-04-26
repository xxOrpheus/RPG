<?php
namespace David\World\Maps;
use David\Client;
use David\Game;
use David\World\Map;
use David\World\Tiles\GrassTile;
use David\World\Tiles\GrassHole;
use David\World\Tiles\LongGrass;
use David\World\Tiles\PalmTree1;
use David\World\Tiles\PalmTree2;
use David\World\Tiles\PalmTree3;
use David\World\Tiles\PalmTree4;
use David\World\Tiles\PalmTree5;
use David\World\Tiles\PalmTree6;
use David\World\Tiles\FencePost;
use David\World\Tiles\Flower1;
use David\World\Tiles\Bush1;
use David\World\Tiles\TreeStump1;
use David\World\Tiles\Tree1;

class Town extends Map {
	public function __construct() {
		$this->setName('Town');
		for($x = 0; $x < 25; $x++) {
			for($y = 0; $y < 19; $y++) {
				$this->addTile(new GrassTile(), $x, $y, 0);
			}
		}

		/* Palm Tree */
		$this->addTile(new PalmTree1(), 5, 6, 2);
		$this->addTile(new PalmTree2(), 6, 6, 2);
		$this->addTile(new PalmTree3(), 5, 7, 2);
		$this->addTile(new PalmTree4(), 6, 7, 2);
		$this->addTile(new PalmTree5(), 5, 8, 0);
		$this->addTile(new PalmTree6(), 6, 8, 0);

		$this->addTile(new GrassHole(), 7, 7, 0);
		$this->addScriptTile(7, 7, function(Client $c, Game $g) {
			$c->sendMessage('You fell in a hole');
			$g->loadMap($c, new Cave());
		});
		$this->addTile(new LongGrass(), 6, 7, 0);
		$this->addTile(new LongGrass(), 6, 6, 0);
		$this->addTile(new LongGrass(), 8, 6, 0);
		$this->addTile(new LongGrass(), 8, 7, 0);
		$this->addTile(new LongGrass(), 7, 6, 0);
		$this->addTile(new Flower1(), 3, 3, 0);
		$this->addTile(new Bush1(), 12, 5, 0);
		//$this->addTile(new TreeStump1(), 13, 15, 0);
		$this->addTile(new Tree1(), 9, 11, 0);
	}
}