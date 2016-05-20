<?php

require_once dirname(dirname(dirname(__FILE__))) . '/path.php';
require_once TTT_PATH . '/include/class/Action.php';
require_once TTT_PATH . '/include/class/response/Response.php';
require_once TTT_PATH . '/include/class/response/ResponseAttachment.php';
require_once TTT_PATH . '/include/class/actions/ActionFactory.php';

/**
 * Action class to get a list of all the Commands
 */

class ActionHelp extends Action {

	public function execute() {
		$this->response->setText(Response::boldText("Commands"));

		// Loop through the list of Actions and get their title, usage, descriptions, and caveats
		foreach (ActionFactory::$actionClasses as $className) {
			require_once ActionFactory::getClassPath($className);
			$attachment = new ResponseAttachment();
			$attachment->setColor($className::getDisplayColor());
			$attachment->addText(Response::boldText($className::getTitle()) . ": " . Response::codeText($className::getUsageExample()));
			$attachment->addTextOnNewLine($className::getDescription());
			foreach ($className::getCaveats() as $caveat) {
				$attachment->addTextOnNewLine("($caveat)");
			}
			$this->response->addAttachment($attachment);
		}
	}

	public static function getTitle() {
		return "Help";
	}

	public static function getUsageExample() {
		return "/tictactoe help";
	}

	public static function getDescription() {
		return "List all the commands and it's usage";
	}

	public static function getDisplayColor() {
		return ResponseAttachment::COLOR_RED;
	}

	public static function getResponseType() {
		return Response::EPHEMERAL;
	}
}
