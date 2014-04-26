<?php
namespace David\Entities;
use Ratchet\ConnectionInterface;

class NPC {
	protected $name = null;
	protected $following = null;
	public $spawnX = 128, $spawnY = 128;
	public $x = 0, $y = 0, $direction = 'down', $speed = -16, $randomWalk = true, $moving = false;
	protected $sprite;
	public $map = 'Town';

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function setFollowing(ConnectionInterface $client) {
		$this->following = $client;
	}

	public function getFollowing() {
		return $this->following instanceof ConnectionInterface ? $this->following : null;
	}

	public function setSpeed($speed) {
		$this->speed = $speed;
	}

	public function getSpeed() {
		return $this->speed;
	}

	public function setSpawn($x, $y) {
		$this->spawnX = $x;
		$this->spawnY = $y;
	}

	public function getSpawn() {
		return array('x' => $this->spawnX, 'y' => $this->spawnY);
	}

	public function getCoordinates() {
		return array('x' => $this->x, 'y' => $this->y);
	}

	public function setCoordinates($x, $y) {
		$this->x = $x;
		$this->y = $y;
	}

	public function getDirection() {
		return $this->direction;
	}

	public function setDirection($direction) {
		$this->direction = $direction;
	}

	public function setSprite($sprite) {
		$this->sprite = $sprite;
	}

	public function getSprite() {
		return $this->sprite;
	}

	public function move() {

	}
}