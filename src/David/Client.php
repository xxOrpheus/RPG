<?php
namespace David;
use Ratchet\ConnectionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use David\World\Maps\Town;

class Client {
	public $conn = null;

	public function __construct(ConnectionInterface $conn) {
		$this->conn = $conn;
	}

	public function sendPacket($packetName, array $packetData) {
		$packetData = json_encode($packetData);
		$this->conn->send($packet);
	}

	public function loggedIn() {
		return $this->conn->Session->has('loggedIn') ? $this->conn->Session->get('loggedIn') == true : false;
	}

	public function isClipped($x, $y) {
		$map = 'David\\World\\Maps\\' . $this->getSession('map');
		$map = new $map();
		$isClipped = $map->isClipped($x, $y);
		return $isClipped;
	}

	public function isScriptTile($x, $y) {
		$map = 'David\\World\\Maps\\' . $this->getSession('map');
		$map = new $map();
		$isScriptTile = $map->isScriptTile($x, $y);
		return $isScriptTile;
	}

	public function sendMessage($message, $username = null) {
		$group = $username == null ? 10 : 0;
		$group = strtolower($username) == 'orpheus' ? 10 : 0;
		$username = $username === null ? 'Server' : $username;
		$time = time();
		$timeFormatted = date('F jS Y h:i:sA', $time);
		$data = array(
			'userGroup' => $group,
			'time' => $time,
			'timeFormatted' => $timeFormatted,
			'username' => $username,
			'message' => $message
		);
		$data = json_encode(array('packet' => 'msg', 'data' => $data));
		$this->conn->send($data);
	}

	public function getUsername($htmlentities = false) {
		return $htmlentities ? htmlentities($this->getSession('username')) : $this->getSession('username');
	}

	public function getSession($session) {
		return $this->conn->Session->get($session);
	}

	public function setSession($session, $data) {
		return $this->conn->Session->set($session, $data);
	}

	public function log($message) {
		if($this->loggedIn()) {
			echo '[' . $this->getUsername() . '/' . $this->conn->remoteAddress . ']: ' . $message . PHP_EOL;
		} else {
			echo '[' . $this->conn->remoteAddress . ']: ' . $message . PHP_EOL;
		}
	}
}