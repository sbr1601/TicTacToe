<?php

require_once dirname(dirname(dirname(__FILE__))) . '/path.php';
require_once TTT_PATH . '/include/class/Action.php';
require_once TTT_PATH . '/include/class/response/Response.php';
require_once TTT_PATH . '/include/class/response/ResponseAttachment.php';
require_once TTT_PATH . '/include/class/db/DBGames.php';

/**
 * Action class to Create a new game
 */

class ActionCreate extends Action {

	public function execute() {
		// Verify that there isn't already an active game in the channel
		$activeGame = DBGames::getActiveGameByChannel($this->params->getChannelId());
		if (!empty($activeGame)) {
			throw new Exception("There is an already an active game in this channel");
		}

		// Verify that the user is not trying to create a game against themselves
		if ($this->params->getUserName() === $this->params->getOpponentName()) {
			throw new Exception("You cannot create a game against yourself");
		}

		// TODO: Find a way to verify that the Opponent user_name is valid. LDAP?

		// Create a new game in the Database
		DBGames::create($this->params->getTeamId(), $this->params->getChannelId(), $this->params->getUserName(), $this->params->getOpponentName());

		// Let the Slack channel know that there's a new game. And notify the opponent that they can Accept or Deny
		$formattedOpponent = Response::boldText(Response::linkUser($this->params->getOpponentName()));
		$accept = Response::boldText("Accept");
		$deny = Response::boldText("Deny");

		$this->response->addAttachment(
			(new ResponseAttachment())
			->setFallback("{$this->params->getUserName()} has created a new game against {$this->params->getOpponentName()}")
			->setPretext("{$this->params->getUserName()} has created a new game")
			->addText("$formattedOpponent: You can $accept or $deny")
			->setColor(self::getDisplayColor())
		);
	}

	public function validateParams() {
		return $this->params->getOpponentName() !== null;
	}

	public static function getTitle() {
		return "Create";
	}

	public static function getUsageExample() {
		return "/tictactoe create opp=userName";
	}

	public static function getDescription() {
		return "Create a new game against userName";
	}

	public static function getDisplayColor() {
		return ResponseAttachment::COLOR_ORANGE;
	}
}
