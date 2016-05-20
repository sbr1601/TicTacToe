<?php

require_once dirname(dirname(dirname(__FILE__))) . '/path.php';
require_once TTT_PATH . '/include/class/Action.php';
require_once TTT_PATH . '/include/class/response/Response.php';
require_once TTT_PATH . '/include/class/response/ResponseAttachment.php';
require_once TTT_PATH . '/include/class/Move.php';
require_once TTT_PATH . '/include/class/GameStatus.php';
require_once TTT_PATH . '/include/class/db/DBGames.php';
require_once TTT_PATH . '/include/class/GameBoard.php';

/**
 * Action class to make at Move on the TicTacToe board
 */

class ActionMove extends Action {

	public function execute() {
		// Verify that there is an active game in the channel
		$activeGame = DBGames::getActiveGameByChannel($this->params->getChannelId());
		if (empty($activeGame)) {
			throw new Exception("There is no active game in this channel");
		}

		$gameId = $activeGame->getGameId();

		// Verify that the game has been accepted
		if ($activeGame->getStatus() !== GameStatus::ACCEPTED) {
			throw new Exception("The challenge has not been accepted yet");
		}
		// Verify that it is the turn of the user trying to make a move
		if ($activeGame->getNextMoverName() !== $this->params->getUserName()) {
			throw new Exception("Only {$activeGame->getNextMoverName()} can make the next move");
		}

		// Get the list of moves represented via an object of `GameBoard`
		$gameBoard = GameBoard::getBoardFromDB($activeGame);

		$nextMove = (new Move())
			->setGameID($gameId)
			->setPosX($this->params->getPosX())
			->setPosY($this->params->getPosY())
			->setMoverName($this->params->getUserName());

		// Verify that the new move can be made on the current board
		// Also verifies if the move is valid
		$gameBoard->addMoveToBoard($nextMove);

		// If the move is valid, update the database with the new move
		DBMoves::add($gameId, $this->params->getPosX(), $this->params->getPosY(), $this->params->getUserName());

		// Send a response to the channel letting everyone know of the latest move
		$this->response->setText(Response::linkUser($this->params->getUserName()) . " has made a move at ({$this->params->getPosX()},{$this->params->getPosY()})");

		$attachment = new ResponseAttachment();

		// Check if this was the winning move
		if ($winner = $gameBoard->getWinner()) {
			// Update the DB and the channel that the game has been won
			DBGames::won($gameId, $this->params->getUserName());
			$attachment->addText("We have a " . Response::boldText("WINNER") . "!!! " . Response::EMOJI_GRIN);
		// Check if the board is full
		} else if ($gameBoard->isBoardFull()) {
			// Update the DB and the channel that the game has been drawn
			DBGames::draw($gameId);
			$attachment->addText("It's a " . Response::boldText("DRAW") . "... " . Response::EMOJI_DISAPPOINTED);
		// If the game is stll undecided, let the next mover know that it's their turn
		} else {
			$nextMoverName = ($this->params->getUserName() === $activeGame->getCreatorName()) ? $activeGame->getOpponentName() : $activeGame->getCreatorName();
			// Update the DB with the next mover's user_name
			DBGames::updateNextMover($gameId, $nextMoverName);
			$attachment->addText(Response::boldText(Response::linkUser($nextMoverName)) . ": It's your turn");
		}

		$attachment->setColor(self::getDisplayColor());
		$this->response->addAttachment($attachment);
		// Display the current board
		$this->response->addAttachment($gameBoard->getBoardAsAttachment());
	}

	public function validateParams() {
		return $this->params->getPosX() !== null && $this->params->getPosY() !== null;
	}

	public static function getTitle() {
		return "Move";
	}

	public static function getUsageExample() {
		return "/tictactoe move x=1 y=1";
	}

	public static function getDescription() {
		return "Make a move at (1,1) on a 3x3 grid";
	}

	public static function getCaveats() {
		return array(
			Response::codeText("x") . " and " . Response::codeText("y") . " should be between 1 and 3",
			"Move can only be made by the player who's turn it is",
		);
	}

	public static function getDisplayColor() {
		return ResponseAttachment::COLOR_ORANGE;
	}
}
