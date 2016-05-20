<?php

require_once dirname(dirname(dirname(__FILE__))) . '/path.php';
require_once TTT_PATH . '/include/class/Action.php';
require_once TTT_PATH . '/include/class/response/Response.php';
require_once TTT_PATH . '/include/class/response/ResponseAttachment.php';
require_once TTT_PATH . '/include/class/GameStatus.php';
require_once TTT_PATH . '/include/class/db/DBGames.php';

/**
 * Action class to Accept a challenge
 */

class ActionAccept extends Action {

	public function execute() {
		// Verify that there is an active game in the channel
		$activeGame = DBGames::getActiveGameByChannel($this->params->getChannelId());
		if (!$activeGame) {
			throw new Exception("There is no active game in this channel");
		}
		// Verify that the game has not been accepted yet
		if ($activeGame->getStatus() !== GameStatus::CREATED) {
			throw new Exception("There is no game challenge to be accepted");
		}
		// Only the opponent can accept the challenge
		if ($activeGame->getOpponentName() !== $this->params->getUserName()) {
			throw new Exception("Only {$activeGame->getOpponentName()} can accept the challenge");
		}

		// Update the game's status in the database as Accepted
		DBGames::accept($activeGame->getGameId());

		// Send a response to the channel letting the game's creator know it's their turn
		$formattedOpponent = Response::boldText(Response::linkUser($activeGame->getCreatorName()));
		$move = Response::boldText("Move");

		$this->response->addAttachment(
			(new ResponseAttachment())
			->setFallback("{$this->params->getUserName()} has accepted the challenge against {$activeGame->getCreatorName()}")
			->setPretext("{$this->params->getUserName()} has accepted the challenge")
			->addText("$formattedOpponent: It's your $move")
			->setColor(self::getDisplayColor())
		);
	}

	public static function getTitle() {
		return "Accept";
	}

	public static function getUsageExample() {
		return "/tictactoe accept";
	}

	public static function getDescription() {
		return "Accept the challenge";
	}

	public static function getCaveats() {
		return array(
			"Only the opponent can accept the challenge",
		);
	}

	public static function getDisplayColor() {
		return ResponseAttachment::COLOR_GREEN;
	}
}
