<?php

require_once dirname(dirname(__FILE__)) . '/path.php';
require_once TTT_PATH . '/include/class/response/Response.php';

/**
 * Abstract class to be extended by all Action classes
 */

abstract class Action {
	protected $params;
	protected $response;

	public function __construct($params) {
		$this->params = $params;
		if (!$this->validateParams()) {
			throw new Exception("Invalid request paramters, please check your request");
		}
		$this->response = new Response();
	}

	public function validateParams() {
		return true;
	}

	abstract public function execute();

	public function getResponse() {
		$this->response->setResponseType(static::getResponseType());
		return $this->response->build();
	}

	abstract public static function getTitle();
	abstract public static function getUsageExample();
	abstract public static function getDescription();

	public static function getCaveats() {
		return array();
	}

	public static function getDisplayColor() {
		return "";
	}

	public static function getResponseType() {
		return Response::IN_CHANNEL;
	}
}
