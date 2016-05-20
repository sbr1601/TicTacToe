<?php

class GameStatus {
	const CREATED = 1;
	const ACCEPTED = 2;
	const DENIED = 3;
	const CANCELED = 4;
	const WON = 5;
	const DRAWN = 6;

	private static $_activeStatuses = array (
		self::CREATED,
		self::ACCEPTED,
	);

	public static function getActiveStatuses() {
		return self::$_activeStatuses;
	}
}
