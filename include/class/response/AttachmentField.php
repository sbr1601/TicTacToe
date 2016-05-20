<?php

class AttachmentField {
	const TITLE = "title";
	const VALUE = "value";
	const SHORT = "short";

	const DEFAULT_SHORT = true;

	private $_title;
	private $_value;
	private $_short;

	public function __construct() {
		$this->_value = "";
		$this->_short = self::DEFAULT_SHORT;
		return $this;
	}

	public function setTitle($title) {
		$this->_title = $title;
		return $this;
	}

	public function addValue($value) {
		$this->_value .= $value;
		return $this;
	}

	public function addValueOnNewLine($value) {
		$this->_value .= "\n$value";
		return $this;
	}

	public function setShort($short) {
		$this->_short = $short;
		return $this;
	}

	/**
	 * Convert the `AttachmentField` object into an array that Slack understands
	 */
	public function build() {
		return array (
			self::TITLE => $this->_title,
			self::VALUE => $this->_value,
			self::SHORT => $this->_short,
		);
	}
}