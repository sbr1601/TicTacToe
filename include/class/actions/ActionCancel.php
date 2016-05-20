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

class ActionCancel extends Action {

	public function execute() {
		// Verify that there is an active game in the channel
		$activeGame = DBGames::getActiveGameByChannel($this->params->getChannelId());
		if (empty($activeGame)) {
			throw new Exception("There is no active game in this channel");
		}

		// Only the creator or opponent can cancel the game
		if ($activeGame->getCreatorName() !== $this->params->getUserName() && $activeGame->getOpponentName() !== $this->params->getUserName()) {
			throw new Exception("Only {$activeGame->getCreatorName()} or {$activeGame->getOpponentName()} can end the game");
		}

		// Update the game's status in the database as Canceled
		DBGames::cancel($activeGame->getGameId());

		// Send a response to the channel letting the game's creator know that the game has been canceled
		$opponent = ($activeGame->getCreatorName() === $this->params->getUserName()) ? $activeGame->getOpponentName() : $activeGame->getCreatorName();
		$formattedOpponent = Response::boldText(Response::linkUser($opponent));
		$create = Response::boldText("Create");

		$this->response->addAttachment(
			(new ResponseAttachment())
			->setFallback("{$this->params->getUserName()} has canceled the game against {$opponent}")
			->setPretext("{$this->params->getUserName()} has canceled the game")
			->addText("$formattedOpponent: You can $create a new game")
			->setColor(self::getDisplayColor())
		);
	}

	public static function getTitle() {
		return "Cancel";
	}

	public static function getUsageExample() {
		return "/tictactoe cancel";
	}

	public static function getDescription() {
		return "Cancel the game";
	}

	public static function getCaveats() {
		return array(
			"Only the creator or opponent can cancel the game",
		);
	}

	public static function getDisplayColor() {
		return ResponseAttachment::COLOR_RED;
	}
}
