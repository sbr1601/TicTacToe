<?php

require_once dirname(dirname(__FILE__)) . '/path.php';
require_once TTT_PATH . '/include/class/Params.php';
require_once TTT_PATH . '/include/class/actions/ActionFactory.php';

class Request {
	const TOKEN = "6F8KvcT2KYR9RmSDwj56NGOA";
	const TEAM_ID = "T1AC9UUDB";

	const PARAM_TOKEN = "token";
	const PARAM_TEAM_ID = "team_id";
	const PARAM_CHANNEL_ID = "channel_id";
	const PARAM_USER_NAME = "user_name";
	const PARAM_TEXT = "text";
	const PARAM_OPPONENT_NAME = "opp";
	const PARAM_POS_X = "x";
	const PARAM_POS_Y = "y";

	private static $_requiredParams = array (
		self::PARAM_TOKEN,
		self::PARAM_TEAM_ID,
		self::PARAM_CHANNEL_ID,
		self::PARAM_USER_NAME,
		self::PARAM_TEXT,
	);

	private static $_optionalParams = array (
		self::PARAM_OPPONENT_NAME,
		self::PARAM_POS_X,
		self::PARAM_POS_Y,
	);

	private $_action;
	private $_request;
	private $_params;

	public function __construct($request) {
		$this->_request = $request;
		if (!$this->_validateRequest()) {
			throw new Exception("Sorry!!! Input request was invalid. Please check your request");
		}

		$this->_params = new Params($request[self::PARAM_TEAM_ID], $request[self::PARAM_CHANNEL_ID], $request[self::PARAM_USER_NAME]);
		$this->_parseInputText();
	}

	public function getAction() {
		return $this->_action;
	}

	public function getParams() {
		return $this->_params;
	}

	/**
	 * Validate the input request
	 *
	 * @return boolean - is the request valid
	 */
	private function _validateRequest() {
		// Verify that all the required parameters are present
		foreach (self::$_requiredParams as $param) {
			if (!array_key_exists($param, $this->_request)) {
				return false;
			}
		}

		// Verify that the request has the correct token and team information
		return (
			$this->_request[self::PARAM_TOKEN] === self::TOKEN && 
			$this->_request[self::PARAM_TEAM_ID] === self::TEAM_ID
		);
	}

	/**
	 * Parse the input text in the request
	 */
	private function _parseInputText() {
		$inputText = preg_split('/\s+/', $this->_request[self::PARAM_TEXT]);

		// Verify that the input action is valid, default to HELP
		$this->_action = $inputText[0];
		if (!in_array($this->_action, array_keys(ActionFactory::$actionClasses))) {
			error_log("Invalid action={$this->_action}");
			$this->_action = ActionFactory::HELP;
		}

		// Update $this->_params with all the optional params
		$actionParams = array();
		foreach ($inputText as $value) {
			if (strpos($value, "=") !== false) {
				list($key, $val) = explode("=", $value);
				switch ($key) {
					case self::PARAM_OPPONENT_NAME:
						$this->_params->setOpponentName($val);
						break;
					case self::PARAM_POS_X:
						$this->_params->setPosX($val);
						break;
					case self::PARAM_POS_Y:
						$this->_params->setPosY($val);
						break;
				}
			}
		}
	}
}
