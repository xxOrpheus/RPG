<?php
namespace David\Entities\NPCs;
use David\Entities\NPC;

class Man extends NPC {
	public function __construct() {
		$this->setName('Man');
		$this->setSprite('player-2.png');
	}
}