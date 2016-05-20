<?php

require_once dirname(dirname(__FILE__)) . '/include/path.php';
require_once TTT_PATH . '/include/class/Request.php';
require_once TTT_PATH . '/include/class/response/Response.php';
require_once TTT_PATH . '/include/class/actions/ActionFactory.php';

try {
	// Let's the client know that the response will be in JSON format
	header('Content-type: application/json');

	// Parse the input request, validate it
	$request = new Request($_POST);

	// Determine which Action class the request belongs to
	$action = ActionFactory::getActionClass($request->getAction(), $request->getParams());
	$action->execute();

	// Send the response back to the client
	echo $action->getResponse();

} catch (Exception $e) {
	// All exceptions are sent back as private (ephemeral) messages
	echo Response::getExceptionMessage($e);
}
