<?php

class Move {
	private $_moveId;
	private $_gameId;
	private $_posX;
	private $_posY;
	private $_moverName;

	public function __construct() {
		return $this;
	}

	public function getMoveId() {
		return $this->_moveId;
	}

	public function setMoveId($moveId) {
		$this->_moveId = (int) $moveId;
		return $this;
	}

	public function getGameId() {
		return $this->_gameId;
	}

	public function setGameId($gameId) {
		$this->_gameId = (int) $gameId;
		return $this;
	}

	public function getPosX() {
		return $this->_posX;
	}

	public function setPosX($posX) {
		$this->_posX = (int) $posX;
		return $this;
	}

	public function getPosY() {
		return $this->_posY;
	}

	public function setPosY($posY) {
		$this->_posY = (int) $posY;
		return $this;
	}

	public function getMoverName() {
		return $this->_moverName;
	}

	public function setMoverName($moverName) {
		$this->_moverName = $moverName;
		return $this;
	}
}
