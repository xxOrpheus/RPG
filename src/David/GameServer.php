<?php
namespace David;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use David\Packets\Move;
use David\Packets\Message;
use David\Packets\Command;
use David\Game;
use David\World\Maps\Town;
use David\Entities\NPC;
use David\Config;

define('PASSWORD_SALT', Config::password_salt);

class GameServer implements MessageComponentInterface {
	protected $clients;
	protected $pdo = null;
	public $lastUpdate = 0;
	protected $walking = null;
	protected $game = null;

	public function __construct() {
		echo '[Game]: Started on ' . date('F j D h:i:sA', time()) . PHP_EOL;
		$this->clients = new \SplObjectStorage;
		$this->pdo = new \PDO('mysql:dbname=' . Config::mysql_database . ';host=' . Config::mysql_host, Config::mysql_username, Config::mysql_password);
		$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->game = new Game($this);
	}

	public function getClients() {
		return $this->clients;
	}

	public function getGame() {
		return $this->game;
	}

	public function getPDO() {
		return $this->pdo;
	}

	public function onOpen(ConnectionInterface $conn) {
		//$conn->Session->set('loggedIn', false);
		$client = new Client($conn);
		$conn->Session->set('client', $client);
		$this->game->loadMap($client, new Town());
		$this->clients->attach($conn);
		$this->update(true);
		echo '[' . $conn->remoteAddress . ']: Connected' . PHP_EOL;
	}

	public function onMessage(ConnectionInterface $conn, $msg) {
		$this->lastUpdate = time();
		$resp = json_decode($msg);
		$this->handleCommand($conn, $resp);
	}

	public function onClose(ConnectionInterface $conn) {
		$this->game->saveLocation($conn->Session->get('client'));
		$conn->Session->get('client')->log('Disconnected');
		$this->clients->detach($conn);
		$this->game->removeNulls();
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		echo 'Oh shit! An error: ' . $e->getMessage() . PHP_EOL;
		file_put_contents('error.txt', $e->getTraceAsString());
	}

	public function consoleLog(ConnectionInterface $client, $msg) {
		if($this->game->loggedIn($client)) {
			echo '[' . $client->Session->get('username') . '/' . $client->remoteAddress . ']: ' . $msg . PHP_EOL;
		} else {
			echo '[' . $client->remoteAddress . ']: ' . $msg . PHP_EOL;
		}
	}

	public function update($newConnection = false) {
		$clients = $this->getClients();
		$entities = $this->game->getEntities();
		if(count($clients) == 0) {
			return;
		}
		$players = array();
		foreach($clients as $client) {
			if($this->getGame()->loggedIn($client)) {
				$x = $client->Session->get('x');
				$y = $client->Session->get('y');
				$direction = $client->Session->get('direction');
				$moving = $client->Session->get('moving');
				$map = $client->Session->get('map');
				if($moving || $newConnection) {
					$players[$map][] = array('username' => $client->Session->get('username'), 'coordinates' => array('x' => $x, 'y' => $y), 'direction' => $direction, 'moving' => $moving);
				}
			}
		}
		$entityData = array();
		foreach($entities as $id => $entity) {
			if($entity instanceof NPC) {
				$name =  $entity->getName();
				$sprite = $entity->getSprite();
				if(($player = $entity->getFollowing()) != null) {
					if($player->Session->get('loggedIn') == true && $player->Session->get('moving') == true || $newConnection) {
						$pX = $player->Session->get('x');
						$pY = $player->Session->get('y');
						$pD = $player->Session->get('direction');
						$mod = 32 - $entity->getSpeed();
						if($pD == 'up') {
							$entity->setDirection('up');
							$entity->setCoordinates($pX, $pY + $mod);
						} else if($pD == 'down') {
							$entity->setDirection('down');
							$entity->setCoordinates($pX, $pY - $mod);
						} else if($pD == 'left') {
							$entity->setDirection('left');
							$entity->setCoordinates($pX + $mod, $pY);
						} else if($pD == 'right') {
							$entity->setDirection('right');
							$entity->setCoordinates($pX - $mod, $pY);
						}
						$entity->moving = true;
					} else {
						$entity->moving = false;
					}
				} else if($entity->randomWalk == true) {
					if(mt_rand(1, 100) > 90) {
						$x = $entity->x;
						$y = $entity->y;
						$change = mt_rand(1, 10) > 4 ? 'x' : 'y';
						$randX = mt_rand(-16, 16);
						$randY = mt_rand(-16, 16);
						$rangeX1 = $entity->spawnX + (32 * 4);
						$rangeX2 = $entity->spawnX - (32 * 4);
						$rangeY1 = $entity->spawnY - (32 * 4);
						$rangeY2 = $entity->spawnY - (32 * 4);
						if($x > $rangeX1) {
							$randX = -8;
						}
						if($x < $rangeX2) {
							$randX = 8;
						}
						if($y > $rangeY1) {
							$randY = -8;
						}
						if($y < $rangeY2) {
							$randY = 8;
						}
						if($change == 'x') {
							if($randX == 0) {
								$randX = 4;
							}
							$entity->setDirection($randX < 0 ? 'left' : 'right');
							$entity->setCoordinates($x + $randX, $y);
						} else if($change == 'y') {
							if($randY == 0) {
								$randY = 4;
							}
							$entity->setDirection($randY < 0 ? 'up' : 'down');
							$entity->setCoordinates($x, $y + $randY);
						}
						$entity->moving = true;
					} else {
						$entity->moving = false;
					}
				}
				$coordinates = $entity->getCoordinates();
				$direction = $entity->getDirection();
				$map = $entity->map;
				$entity = array('id' => $id, 'name' => $name, 'sprite' => $sprite, 'type' => 'npc', 'coordinates' => $coordinates, 'direction' => $direction, 'moving' => $entity->moving);
				if($entity->moving == true || $newConnection) {
					$entityData[$map][] = $entity;
				}
			} else if($entity instanceof Item) {
				$entityData[] = array('name' => $entity->getName(), 'type' => 'item');
			}
		}
		$playerData = array('packet' => 'playerUpdate', 'players' => $players);
		$entityCount = count($entityData);
		foreach($clients as $client) {
			$map = $client->Session->get('map');
			if(isset($playerData['players'][$map])) {
				$data = $playerData;
				$data['players'] = $data['players'][$map];
				$data = json_encode($data);
				$client->send($data);
			}
			if(isset($entityData[$map])) {
				$data = $entityData[$map];
				$data = array('packet' => 'entityUpdate', 'entities' => $data);
				$data = json_encode($data);
				$client->send($data);
			}
		}
	}

	public function handleCommand(ConnectionInterface $conn, $resp) {
		$client = $conn->Session->get('client');
		if($client == null) {
			return;
		}
		if($resp->command != 'move' && $resp->command != 'stoppedMoving' && $resp->command != 'msg') {
			echo '[' . $conn->remoteAddress . ']: ';
			var_dump($resp);
		}
		switch($resp->command) {
			case 'requestMap':
				if(!empty($client->getSession('map'))) {
					$map = $client->getSession('map');
					$map = 'David\\World\\Maps\\' . $map;
					$map = new $map();
					$this->game->loadMap($client, $map);
				} else {
					$map = new Town();
					$this->game->loadMap($client, $map);
				}
				$this->game->loadMap($client, new $map());
				break;

			case 'stoppedMoving':
				$client->setSession('moving', false);
				break;

			case 'move':
				Move::perform($client, $resp, $this->game);
				break;

			case 'msg':
				Message::perform($client, $resp, $this->game);
				break;

			case 'command':
				Command::perform($client, $resp, $this->game);
				break;

			case 'login':
				if(isset($resp->data)) {
					parse_str($resp->data, $query);
					$r = $this->game->login($conn, $query['username'], $query['password']);
					if($r !== true) {
						$client->sendMessage($r);
					} else if($client->loggedIn()) {
						$client->log('Logged in');
						$this->game->restoreLocation($client);
						$this->update(true);
					}
				}
				break;

			case 'register':
				if(isset($resp->data)) {
					parse_str($resp->data, $query);
					$r = $this->game->register($client, $query);
					if($r !== true) {
						$this->game->sendMessage($client, $r);
					} else {
						$r = $this->game->login($client->conn, $query['username'], $query['password']);
						if($r !== true) {
							$this->sendMessage($client, $r);
						} else {
							$client->log('Registered');
						}
					}
				}
				break;

			case 'logout':
				$client->sendMessage('Logged out');
				$client->log('Logged out');
				$this->game->saveLocation($conn->Session->get('client'));
				$this->game->sendStatus($client->conn, false);
				$client->conn->Session->set('loggedIn', false);
				$client->conn->Session->set('username', null);
				$client->conn->Session->set('x', 0);
				$client->conn->Session->set('y', 0);
				break;
		}
	}
}