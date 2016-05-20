<?php

require_once dirname(dirname(dirname(__FILE__))) . '/path.php';
require_once TTT_PATH . '/include/class/Action.php';
require_once TTT_PATH . '/include/class/response/Response.php';
require_once TTT_PATH . '/include/class/response/ResponseAttachment.php';
require_once TTT_PATH . '/include/class/GameStatus.php';
require_once TTT_PATH . '/include/class/db/DBGames.php';

/**
 * Action class to Deny a challenge
 */

class ActionDeny extends Action {

	public function execute() {
		// Verify that there is an active game in the channel
		$activeGame = DBGames::getActiveGameByChannel($this->params->getChannelId());
		if (empty($activeGame)) {
			throw new Exception("There is no active game in this channel");
		}
		// Verify that the game has not been accepted or denied yet
		if ($activeGame->getStatus() !== GameStatus::CREATED) {
			throw new Exception("There is no game challenge to be denied");
		}
		// Only the opponent can deny the challenge
		if ($activeGame->getOpponentName() !== $this->params->getUserName()) {
			throw new Exception("Only {$activeGame->getOpponentName()} can deny the challenge");
		}

		// Update the game's status in the database as Denied
		DBGames::deny($activeGame->getGameId());

		// Send a response to the channel letting the game's creator know that the game has been canceled
		$formattedOpponent = Response::boldText(Response::linkUser($activeGame->getCreatorName()));
		$create = Response::boldText("Create");

		$this->response->addAttachment(
			(new ResponseAttachment())
			->setFallback("{$this->params->getUserName()} has denied the challenge against {$activeGame->getCreatorName()}")
			->setPretext("{$this->params->getUserName()} has denied the challenge")
			->addText("$formattedOpponent: You can $create a new game")
			->setColor(self::getDisplayColor())
		);
	}

	public static function getTitle() {
		return "Deny";
	}

	public static function getUsageExample() {
		return "/tictactoe deny";
	}

	public static function getDescription() {
		return "Deny the challenge";
	}

	public static function getCaveats() {
		return array(
			"Only the opponent can deny the challenge",
		);
	}

	public static function getDisplayColor() {
		return ResponseAttachment::COLOR_RED;
	}
}
