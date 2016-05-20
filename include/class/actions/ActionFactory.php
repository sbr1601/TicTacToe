<?php

require_once dirname(dirname(dirname(__FILE__))) . '/path.php';

class ActionFactory {
	const CREATE = "create";
	const ACCEPT = "accept";
	const DENY = "deny";
	const CANCEL = "cancel";
	const MOVE = "move";
	const STATUS = "status";
	const HELP = "help";

	public static $actionClasses = array (
		self::CREATE => "ActionCreate",
		self::ACCEPT => "ActionAccept",
		self::DENY => "ActionDeny",
		self::CANCEL => "ActionCancel",
		self::MOVE => "ActionMove",
		self::STATUS => "ActionStatus",
		self::HELP => "ActionHelp",
	);

	/**
	 * Create an object of the corresponding `Action` class from the input
	 *
	 * @param action - name of the action that needs to be executed
	 * @param params - object of `Param` that the action class needs for execution
	 * @return correponsing `Action` class object
	 */
	public static function getActionClass($action, $params) {
		$className = self::$actionClasses[$action];
		require_once self::getClassPath($className);
		return new $className($params);
	}

	public static function getClassPath($className) {
		return TTT_PATH . "/include/class/actions/{$className}.php";
	}
}