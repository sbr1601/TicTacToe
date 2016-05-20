<?php

class Game {
	private $_gameId;
	private $_teamId;
	private $_channelId;
	private $_creatorName;
	private $_opponentName;
	private $_nextMoverName;
	private $_winnerName;
	private $_status;

	public function __construct() {
		return $this;
	}

	public function getGameId() {
		return $this->_gameId;
	}

	public function setGameId($gameId) {
		$this->_gameId = (int) $gameId;
		return $this;
	}

	public function getTeamId() {
		return $this->_teamId;
	}

	public function setTeamId($teamId) {
		$this->_teamId = $teamId;
		return $this;
	}

	public function getChannelId() {
		return $this->_channelId;
	}

	public function setChannelId($channelId) {
		$this->_channelId = $channelId;
		return $this;
	}

	public function getCreatorName() {
		return $this->_creatorName;
	}

	public function setCreatorName($creatorName) {
		$this->_creatorName = $creatorName;
		return $this;
	}

	public function getOpponentName() {
		return $this->_opponentName;
	}

	public function setOpponentName($opponentName) {
		$this->_opponentName = $opponentName;
		return $this;
	}

	public function getNextMoverName() {
		return $this->_nextMoverName;
	}

	public function setNextMoverName($nextMoverName) {
		$this->_nextMoverName = $nextMoverName;
		return $this;
	}

	public function getWinnerName() {
		return $this->_winnerName;
	}

	public function setWinnerName($winnerName) {
		$this->_winnerName = $winnerName;
		return $this;
	}

	public function getStatus() {
		return $this->_status;
	}

	public function setStatus($status) {
		$this->_status = (int) $status;
		return $this;
	}
}
