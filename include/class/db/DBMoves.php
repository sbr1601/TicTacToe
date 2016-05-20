<?php

require_once dirname(dirname(dirname(__FILE__))) . '/path.php';
require_once TTT_PATH . '/include/class/db/DBConnection.php';
require_once TTT_PATH . '/include/class/Move.php';

class DBMoves {

	/**
	 * Add a new move
	 *
	 * @param gameId where the move was made
	 * @param posX, posY positions on the board where the move was made
	 * @param moverName user_name of the player making the move
	 */
	public static function add($gameId, $posX, $posY, $moverName) {
		$tttDB = new DBConnection('tttWrite');

		$sql  = "INSERT INTO moves(gameId, posX, posY, moverName) VALUES (";
		$sql .= $tttDB->quote($gameId, DBConnection::INT) . ", " . $tttDB->quote($posX, DBConnection::INT) . ", ";
		$sql .= $tttDB->quote($posY, DBConnection::INT) . ", " . $tttDB->quote($moverName) . ")";

		$tttDB->query($sql);
	}

	/**
	 * Get all the moves for a game
	 *
	 * @param gameId for which the moves need to be retrieved
	 * @return list of all the moves made in that game
	 */
	public static function getMovesByGame($gameId) {
		$tttDB = new DBConnection('tttRead');
		$sql = "SELECT posX, posY, moverName FROM moves WHERE gameId = " . $tttDB->quote($gameId, DBConnection::INT);
		$dbMoves = $tttDB->queryToArray($sql);

		if (empty($dbMoves)) {
			return false;
		}

		$moves = array();
		foreach ($dbMoves as $move) {
			$moves[] = (
				(new Move())
				->setPosX($move["posX"])
				->setPosY($move["posY"])
				->setMoverName($move["moverName"])
			);
		}
		return $moves;
	}
}