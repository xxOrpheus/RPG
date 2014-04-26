<?php
namespace David;
use David\GameServer;
use David\Entities\NPC;
use David\Entities\NPCs\Man;
use David\World\Map;
use David\World\Maps\Town;
use Ratchet\ConnectionInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class Game {
	public $gs = null;
	public $entities = array();

	public function __construct(GameServer $gs) {
		$this->gs = $gs;
	}

	public function addNPC(NPC $npc) {
		$this->entities[] = $npc;
		$npc->setCoordinates($npc->spawnX, $npc->spawnY);
	}

	public function getEntities() {
		return $this->entities;
	}

	public function sendMessage($msg, $username = null) {
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
			'message' => $msg
		);
		$data = json_encode(array('packet' => 'msg', 'data' => $data));
		foreach($this->gs->getClients() as $client) {
			$client->send($data);
		}
	}

	public function loadMap(Client $client, Map $map) {
		$client->setSession('map', $map->getName());
		$client = $client->conn;
		$data = array('packet' => 'mapLoad', 'map' => $map->tiles, 'map_name' => $map->getName());
		$data = json_encode($data);
		$client->send($data);
		$this->removeNulls();
	}

	public function removeNulls() {
		$players = array();
		$clients = $this->gs->getClients();
		foreach($clients as $c) {
			if($this->loggedIn($c)) {
				$map = $c->Session->get('map');
				$players[$map][] = array('username' => $c->Session->get('username'));
			}
		}
		$players = array('packet' => 'removeNulls', 'players' => $players);
		foreach($clients as $c) {
			$map = $c->Session->get('map');
			if(isset($players['players'][$map])) {
				$data = $players;
				$data['players'] = $data['players'][$map];
				$data = json_encode($data);
				$c->send($data);
			}
		}
	}

	public function loggedIn(ConnectionInterface $client) {
		return $client->Session->has('loggedIn') ? $client->Session->get('loggedIn') == true : false;
	}

	public function userLoggedIn($username) {
		foreach($this->gs->getClients() as $client) {
			if($this->loggedIn($client)) {
				if($client->Session->get('username') == $username) {
					return true;
				}
			}
		}
		return false;
	}

	public function sendStatus(ConnectionInterface $client, $state) {
		if($state) {
			$client->send(json_encode(array('packet' => 'status', 'data' => htmlentities(ucfirst($client->Session->get('username'))))));
		} else {
			$client->send(json_encode(array('packet' => 'status', 'data' => false)));
		}
	}

	public function saveLocation(Client $c) {
		$q = $this->gs->getPDO()->prepare('UPDATE players SET x = ?, y = ?, map = ? WHERE username = ?');
		$name = $c->getUsername();
		$x = $c->getSession('x');
		$y = $c->getSession('y');
		$map = $c->getSession('map');
		$r = $q->execute(array($x, $y, $map, $name));
		return true;
	}

	public function restoreLocation(Client $c) {
		$q = $this->gs->getPDO()->prepare('SELECT x, y, map FROM players WHERE username = ?');
		$name = $c->getUsername();
		$q->execute(array($name));
		$r = $q->fetch(\PDO::FETCH_ASSOC);
		$x = $r['x'];
		$y = $r['y'];
		$map = $r['map'];
		$c->setSession('x', $x);
		$c->setSession('y', $y);
		if(!empty($map)) {
			$map = 'David\\World\\Maps\\' . $map;
			$map = new $map();
			$this->loadMap($c, $map);
		} else {
			$map = new Town();
			$this->loadMap($c, $map);
		}
	}

	public function register(Client $client, $q) {
		$client = $client->conn;
		if($this->loggedIn($client)) {
			return 'Already logged in';
		}
		if(!isset($q['username'], $q['password'], $q['email'])) {
			return 'Missing fields';
		}
		$username = $q['username'];
		$password = hash_hmac('sha512', $q['password'], PASSWORD_SALT);
		$email = $q['email'];
		if(!ctype_alnum($username)) {
			return 'Username is alphanumeric';
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return 'Invalid e-mail';
		}
		$len = strlen($username);
		if($len < 3 || $len > 12) {
			return 'Username must be 3-12 characters long';
		}
		$q = $this->gs->getPDO()->prepare('INSERT INTO players(username, password, email, registered) VALUES(?, ?, ?, ?)');
		$r = $q->execute(array($username, $password, $email, time()));
		if(!$r) {
			return 'Username or e-mail already in use';
		}
		return true;
	}


	public function login(ConnectionInterface $client, $username, $password) {
		if($this->userLoggedIn($username)) {
			$client->Session->clear();
			$client->Session->set('loggedIn', false);
			return 'That account is already logged in';
		}
		if($this->loggedIn($client)) {
			$this->sendStatus($client, true);
			return 'Already logged in';
		}
		if(!ctype_alnum($username)) {
			return 'Username is alphanumeric only';
		}
		$len = strlen($username);
		if($len < 3 || $len > 12) {
			return 'Username must be 3-12 characters long';
		}
		$password = hash_hmac('sha512', $password, PASSWORD_SALT);
		$q = $this->gs->getPDO()->prepare('SELECT COUNT(*) as userExists, id, username FROM players WHERE username = ? AND password = ?');
		$q->execute(array($username, $password));
		$r = $q->fetch(\PDO::FETCH_ASSOC);
		if($r['userExists'] > 0) {
			$client->Session->set('id', $r['id']);
			$client->Session->set('loggedIn', true);
			$client->Session->set('username', $r['username']);
			$client->Session->set('direction', 'down');
			$client->Session->set('x', 0);
			$client->Session->set('y', 24);
			$this->sendStatus($client, true);
			return true;
		}
		return 'Invalid username or password';
	}
}
?>