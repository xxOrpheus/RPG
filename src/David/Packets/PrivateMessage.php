<?php
namespace David\Packets;
use Ratchet\ConnectionInterface;
use David\Game;
use David\ezBBC;
use David\Client;

class Message {
	public static function perform(Client $client, $resp, Game $game) {
		if(!$client->loggedIn()) {
			$client->sendMessage('You are not logged in');
		} else {
			$bbc = new ezBBC();
			$resp->data = $bbc->bbcize($resp->data);
			$username = $client->getUsername(true);
			foreach($game->gs->getClients() as $c) {
				$c = $c->Session->get('client');
				if($resp->username == $c->getUsername()) {
					$c->sendMessage($resp->data, '[PM] ' . $username);
					$client->log('PM to ' . $c->getUsername() . ': ' . $resp->data);
					return true;
				}
			}
		}
	}
}