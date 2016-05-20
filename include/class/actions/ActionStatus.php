<?php

require_once dirname(dirname(dirname(__FILE__))) . '/path.php';
require_once TTT_PATH . '/include/class/Action.php';
require_once TTT_PATH . '/include/class/response/Response.php';
require_once TTT_PATH . '/include/class/response/ResponseAttachment.php';
require_once TTT_PATH . '/include/class/response/AttachmentField.php';
require_once TTT_PATH . '/include/class/GameStatus.php';
require_once TTT_PATH . '/include/class/GameBoard.php';
require_once TTT_PATH . '/include/class/db/DBGames.php';

/**
 * Action class to get the current game's Status
 */

class ActionStatus extends Action {

	public function execute() {
		// Verify that there is an active game in the channel
		$activeGame = DBGames::getActiveGameByChannel($this->params->getChannelId());
		if (empty($activeGame)) {
			$this->response->setText("There is no active game in this channel");
			return;
		}

		// Verify that the game has been accepted
		if ($activeGame->getStatus() === GameStatus::CREATED) {
			$this->response->setText("The challenge has not been accepted or denied");
			return;
		}

		$creator = $activeGame->getCreatorName();
		$opponent = $activeGame->getOpponentName();
		$mover = $activeGame->getNextMoverName();

		// Send a response to the user with the game's status
		// It displays the creator's name, opponent's name, and specifies who the next mover is
		$attachment = new ResponseAttachment();
		$attachment->setFallback("Game Status");
		$attachment->addText(Response::boldText("Game Status"));
		$attachment->addField(
			(new AttachmentField())
			->setTitle("Creator")
			->addValue(Response::linkUser($creator))
			->addValueOnNewLine(($creator === $mover) ? "(Next Move)" : "")
		);
		$attachment->addField(
			(new AttachmentField())
			->setTitle("Opponent")
			->addValue(Response::linkUser($opponent))
			->addValueOnNewLine(($opponent === $mover) ? "(Next Move)" : "")
		);

		$attachment->setColor(self::getDisplayColor());
		$this->response->addAttachment($attachment);
		
		// Fetch the game's board from the DB and send it in the response
		$gameBoard = GameBoard::getBoardFromDB($activeGame);
		$this->response->addAttachment($gameBoard->getBoardAsAttachment());
	}

	public static function getTitle() {
		return "Status";
	}

	public static function getUsageExample() {
		return "/tictactoe status";
	}

	public static function getDescription() {
		return "Status of the current game";
	}

	public static function getDisplayColor() {
		return ResponseAttachment::COLOR_GREEN;
	}

	public static function getResponseType() {
		return Response::EPHEMERAL;
	}
}
