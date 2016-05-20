<?php

require_once dirname(dirname(__FILE__)) . '/path.php';
require_once TTT_PATH . '/include/class/response/Response.php';
require_once TTT_PATH . '/include/class/response/ResponseAttachment.php';
require_once TTT_PATH . '/include/class/db/DBMoves.php';

class GameBoard {
	// Text representation of the moves
	const BLANK = "-";
	const EX = "X";
	const OH = "0";

	private $_moveGrid;
	private $_moveCount;
	private $_moveSignatureMap;

	public function __construct($game, $moves) {
		$this->_generateMoveSignatureMap($game);
		$this->fillGrid($moves);
		$this->_moveCount = count($moves);
	}

	/**
	 * Check if the board is full
	 *
	 * @return True if the board is full, False otherwise
	 */
	public function isBoardFull() {
		return $this->_moveCount === 9;
	}

	/**
	 * Add a new move to the existing grid
	 * Also verifies if the move is valid
	 *
	 * @param move - Object of class `Move` to be added to the grid
	 */
	public function addMoveToBoard($move) {
		$x = $move->getPosX();
		$y = $move->getPosY();
		if ($this->_moveGrid[$x][$y] !== self::BLANK) {
			throw new Exception("Move already made at ($x, $y) => {$this->_moveGrid[$x][$y]}");
		}
		$this->_moveGrid[$x][$y] = $this->_getMoveSignature($move);
		$this->_moveCount++;
	}

	/**
	 * Get the user_name of the game's winner
	 *
	 * @return user_name of the winner. FALSE if no winner
	 */
	public function getWinner() {
		// Check the rows
		for ($i = 1; $i <= 3; $i++) {
			$row = $this->_moveGrid[$i];
			if (self::_signaturesMatch($row[1], $row[2], $row[3])) {
				return $this->_getMoverFromSignature($row[1]);
			}
		}

		// Check the columns
		for ($i = 1; $i <= 3; $i++) {
			if (self::_signaturesMatch($this->_moveGrid[1][$i], $this->_moveGrid[2][$i], $this->_moveGrid[3][$i])) {
				return $this->_getMoverFromSignature($this->_moveGrid[1][$i]);
			}
		}

		// Check the diagnols
		if (self::_signaturesMatch($this->_moveGrid[1][1], $this->_moveGrid[2][2], $this->_moveGrid[3][3])) {
			return $this->_getMoverFromSignature($this->_moveGrid[1][1]);
		}
		if (self::_signaturesMatch($this->_moveGrid[1][3], $this->_moveGrid[2][2], $this->_moveGrid[3][1])) {
			return $this->_getMoverFromSignature($this->_moveGrid[1][3]);
		}
	}

	/**
	 * The game's creator has the symbol X
	 * The opponent has symbol 0
	 */
	private function _generateMoveSignatureMap($game) {
		$this->_moveSignatureMap = array (
			$game->getCreatorName() => self::EX,
			$game->getOpponentName() => self::OH,
		);
	}

	public function getMoveSignatureMap() {
		return $this->_moveSignatureMap;
	}

	/**
	 * Get the user_name of the user with the input signature
	 *
	 * @param sig - Signature to be match
	 * @return matching user_name
	 */
	private function _getMoverFromSignature($sig) {
		foreach ($this->_moveSignatureMap as $moverName => $signature) {
			if ($signature === $sig) {
				return $moverName;
			}
		}
		return false;
	}

	private function _getMoveSignature($move) {
		return $this->_moveSignatureMap[$move->getMoverName()];
	}

	/**
	 * Create a blank 3x3 grid, and fill it with the input moves
	 *
	 * @param moves - array of objects of type `Move` to be inserted into the grid
	 */
	private function fillGrid($moves) {
		for ($i = 1; $i <= 3; $i++) {
			for ($j = 1; $j <= 3; $j++) {
				$this->_moveGrid[$i][$j] = self::BLANK;
			}
		}

		if (empty($moves)) {
			return;
		}

		foreach ($moves as $move) {
			$x = $move->getPosX();
			$y = $move->getPosY();
			$this->_moveGrid[$x][$y] = $this->_getMoveSignature($move);
		}
	}

	/**
	 * Verify that the signatures all belong to the same user
	 *
	 * @param sig1, sig2, sig3 - Signatures to be compared
	 * @return True if all 3 signatures match, False otherwise 
	 */
	private static function _signaturesMatch($sig1, $sig2, $sig3) {
		if ($sig1 === self::BLANK || $sig2 === self::BLANK || $sig3 === self::BLANK) {
			return false;
		}
		return ($sig1 === $sig2 && $sig2 === $sig3);
	}

	/**
	 * Display the current board as a visual representation
	 *
	 * @return string - representing the current state of the board
	 */
	private function displayBoard() {
		$display = "";
		for ($i = 1; $i <= 3; $i++) {
			for ($j = 1; $j <= 3; $j++) {
				$display .= " {$this->_moveGrid[$i][$j]} ";
				if ($j !== 3) {
					$display .= "|";
				}
			}

			$display .= "\n";
			if ($i !== 3) {
				$display .= "-----------\n";
			}
		}

		return "$display";
	}

	/**
	 * Display the board as an object of `ResponseAttachment`
	 * Will be displayed on the Slack channel
	 *
	 * @return An object of `ReponseAttachment` to be "attached" to the `Reponse`
	 */
	public function getBoardAsAttachment() {
		$attachment = new ResponseAttachment();
		$attachment->addText(Response::boldText("Board"));
		$attachment->addTextOnNewLine(Response::preformatText($this->displayBoard()));
		foreach ($this->getMoveSignatureMap() as $mover => $signature) {
			$attachment->addTextOnNewLine(Response::codeText("$signature => $mover"));
		}
		$attachment->setColor(ResponseAttachment::COLOR_GREEN);
		return $attachment;
	}

	/**
	 * Helper function to fetch the moves of a game from the DB
	 * And create an object of `GameBoard` with the game's moves
	 *
	 * @param game - an pbject of `Game` who's moves need to be retrieved from the DB
	 * @return an object of `GameBoard` with the game's moves
	 */
	public static function getBoardFromDB($game) {
		$moves = DBMoves::getMovesByGame($game->getGameId());
		return new GameBoard($game, $moves);
	}
}
