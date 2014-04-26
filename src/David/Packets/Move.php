<?php
namespace David\Packets;
use Ratchet\ConnectionInterface;
use David\Game;
use David\Client;

class Move {
	public static function perform(Client $client, $resp, Game $game) {
		if(!$client->loggedIn()) {
			$client->sendMessage('You are not logged in');
		} else {
			if(isset($resp->direction)) {
				$time = round(microtime(true) * 1000);
				$diff = $time - $client->getSession('lastWalk');
				if($diff < 50) {
				//	return;
				}
				$client->setSession('lastWalk', $time);
				$moveDiff = 4;
				$x = $client->getSession('x');
				$y = $client->getSession('y');
				switch($resp->direction) {
					case 'up':
						$client->setSession('direction', 'up');
						if($client->isClipped($x, $y - 32)) {
							break;
						}
						if($y - $moveDiff < 1) {
							break;
						}
						$y -= $moveDiff;
						break;

					case 'left':
						$client->setSession('direction', 'left');
						if($client->isClipped($x - 32, $y)) {
							break;
						}
						if($x - $moveDiff < 1) {
							break;
						}
						$x -= $moveDiff;
						break;

					case 'down':
						$client->setSession('direction', 'down');
						if($client->isClipped($x, $y + 32)) {
							break;
						}
						if($y + $moveDiff > (608 - 32)) {
							break;
						}
						$y += $moveDiff;
						break;

					case 'right':
						$client->setSession('direction', 'right');
						if($client->isClipped($x + 32, $y)) {
							break;
						}
						if($x + $moveDiff > (800 - 32)) {
							break;
						}
						$x += $moveDiff;
						break;
				}
				if(($script = $client->isScriptTile($x, $y)) != null) {
					if($client->getSession('inScriptTile') == false) {
						$client->setSession('inScriptTile', true);
						$script($client, $game);
					}
				} else {
					$client->setSession('inScriptTile', false);
				}
				$client->setSession('x', $x);
				$client->setSession('y', $y);
				$client->setSession('moving', true);
			}
		}
	}
}