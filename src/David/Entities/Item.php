<?php
namespace David\Entities;

class Item{
	protected $name = null;
	protected $following = null; // allow for possible items that follow players

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}
}