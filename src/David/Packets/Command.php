<?php
namespace David\Packets;
use Ratchet\ConnectionInterface;
use David\Game;
use David\ezBBC;
use David\Client;
use David\Entities\NPC;
use David\Entities\NPCs\Man;
use David\World\Maps\Town;

class Command {
	public static function perform(Client $client, $resp, Game $game) {
		if(!$client->loggedIn()) {
			$client->sendMessage('You are not logged in');
			return false;
		}
		$args = explode(' ', $resp->data);
		$command = strtolower($args[0]);
		array_shift($args);
		switch($command) {
			case 'npc':
				$man = new Man();
				$game->addNPC($man);
				$man->setFollowing($client->conn);
				$client->sendMessage('Added man');
				break;

			case 'walkingnpc':
				$man = new Man();
				$man->randomWalk = true;
				$game->addNPC($man);
				break;

			case 'pm':
				if(isset($args[0]) && count($args) > 0) {
					$bbc = new ezBBC();
					$username = $client->getUsername(true);
					$to = $args[0];
					array_shift($args);
					$msg = implode(' ', $args);
					$msg = $bbc->bbcize($msg);
					foreach($game->gs->getClients() as $c) {
						$c = $c->Session->get('client');
						if($to == $c->getUsername()) {
							$c->sendMessage($resp->data, '[PM] ' . $username);
							$client->log('PM to ' . $c->getUsername() . ': ' . $resp->data);
							return true;
						}
					}
				} 
				break;

			case 'home':
				$town = new Town();
				$game->loadMap($client, $town);
				$client->sendMessage('Welcome home');
				break;

			default:
				$client->sendMessage('Invalid command');
				break;
		}
	}
}