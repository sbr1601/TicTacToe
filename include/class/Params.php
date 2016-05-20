<?php

class Params {
	private $_teamId;
	private $_channelId;
	private $_userName;
	private $_opponentName;
	private $_posX;
	private $_posY;

	public function __construct($teamId, $channelId, $userName) {
		$this->_teamId = $teamId;
		$this->_channelId = $channelId;
		$this->_userName = $userName;
		return $this;
	}

	public function getTeamId() {
		return $this->_teamId;
	}

	public function getChannelId() {
		return $this->_channelId;
	}

	public function getUserName() {
		return $this->_userName;
	}

	public function getOpponentName() {
		return $this->_opponentName;
	}

	public function setOpponentName($opponentName) {
		$this->_opponentName = $opponentName;
		return $this;
	}

	public function getPosX() {
		return $this->_posX;
	}

	public function setPosX($posX) {
		if (!self::validPostion($posX)) {
			throw new Exception("Invalid position x=$posX");
		}

		$this->_posX = $posX;
		return $this;
	}

	public function getPosY() {
		return $this->_posY;
	}

	public function setPosY($posY) {
		if (!self::validPostion($posY)) {
			throw new Exception("Invalid position y=$posY");
		}

		$this->_posY = $posY;
		return $this;
	}

	private static function validPostion($pos) {
		return $pos >= 1 && $pos <= 3;
	}
}
