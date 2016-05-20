<?php

require_once dirname(dirname(dirname(__FILE__))) . '/path.php';
require_once TTT_PATH . '/include/class/db/DBConnection.php';
require_once TTT_PATH . '/include/class/GameStatus.php';
require_once TTT_PATH . '/include/class/Game.php';

class DBGames {

	/**
	 * Create a new game
	 *
	 * @param teamId where the game has been created
	 * @param channelId name of the channel in which the game is created
	 * @param creatorName user_name of the user who created the game
	 * @param opponentName user_name of the opponent the creatorName has challenged
	 */
	public static function create($teamId, $channelId, $creatorName, $opponentName) {
		$tttDB = new DBConnection('tttWrite');

		$sql  = "INSERT INTO games(teamId, channelId, creatorName, opponentName, nextMoverName, status) VALUES (";
		$sql .= $tttDB->quote($teamId) . ", " . $tttDB->quote($channelId) . ", ";
		$sql .= $tttDB->quote($creatorName) . ", " . $tttDB->quote($opponentName) . ", ";
		$sql .= $tttDB->quote($creatorName) . ", " . GameStatus::CREATED . ")";

		$tttDB->query($sql);
	}

	/**
	 * Accept the challenge
	 *
	 * @param gameId which needs to be accepted
	 */
	public static function accept($gameId) {
		$tttDB = new DBConnection('tttWrite');
		$sql = "UPDATE games SET status = " . GameStatus::ACCEPTED . " WHERE gameId = " . $tttDB->quote($gameId, DBConnection::INT);
		$tttDB->query($sql);
	}

	/**
	 * Deny the challenge
	 *
	 * @param gameId which needs to be denied
	 */
	public static function deny($gameId) {
		$tttDB = new DBConnection('tttWrite');
		$sql = "UPDATE games SET status = " . GameStatus::DENIED . " WHERE gameId = " . $tttDB->quote($gameId, DBConnection::INT);
		$tttDB->query($sql);
	}

	/**
	 * Cancel the game
	 *
	 * @param gameId which needs to be canceled
	 */
	public static function cancel($gameId) {
		$tttDB = new DBConnection('tttWrite');
		$sql = "UPDATE games SET status = " . GameStatus::CANCELED . " WHERE gameId = " . $tttDB->quote($gameId, DBConnection::INT);
		$tttDB->query($sql);
	}

	/**
	 * The game is drawn
	 *
	 * @param gameId which was drawn
	 */
	public static function draw($gameId) {
		$tttDB = new DBConnection('tttWrite');
		$sql = "UPDATE games SET status = " . GameStatus::DRAWN . " WHERE gameId = " . $tttDB->quote($gameId, DBConnection::INT);
		$tttDB->query($sql);
	}

	/**
	 * The game has been won
	 *
	 * @param gameId which was won
	 * @param winnerName user_name of the winner
	 */
	public static function won($gameId, $winnerName) {
		$tttDB = new DBConnection('tttWrite');
		$sql  = "UPDATE games ";
		$sql .= "SET status = " . GameStatus::WON . " AND winnerName = " . $tttDB->quote($winnerName) . " ";
		$sql .= "WHERE gameId = " . $tttDB->quote($gameId, DBConnection::INT);
		$tttDB->query($sql);
	}

	/**
	 * Update the game with the nextMover
	 *
	 * @param gameId which needs to be update
	 * @param nextMoverName user_name of the next mover
	 */
	public static function updateNextMover($gameId, $nextMoverName) {
		$tttDB = new DBConnection('tttWrite');
		$sql = "UPDATE games SET nextMoverName = " . $tttDB->quote($nextMoverName) . " WHERE gameId = " . $tttDB->quote($gameId, DBConnection::INT);
		$tttDB->query($sql);
	}

	/**
	 * Get the current active game for the input channelId
	 *
	 * @param channelId in which the game is being played
	 * @return gameInfo of the current active game. Returns NULL if none present
	 */
	public static function getActiveGameByChannel($channelId) {
		$tttDB = new DBConnection('tttRead');

		$sql  = "SELECT gameId, creatorName, opponentName, nextMoverName, status FROM games ";
		$sql .= "WHERE channelId = " . $tttDB->quote($channelId) . " ";
		$sql .= "AND status IN (" . $tttDB->quoteCSV(GameStatus::getActiveStatuses(), DBConnection::INT) . ")";

		$games = $tttDB->queryToArray($sql);
		if (empty($games)) {
			return false;
		}

		$game = $games[0];
		return (
			(new Game())
			->setGameId($game["gameId"])
			->setCreatorName($game["creatorName"])
			->setOpponentName($game["opponentName"])
			->setNextMoverName($game["nextMoverName"])
			->setStatus($game["status"])
		);
	}

}
