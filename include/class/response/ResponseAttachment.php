<?php

class ResponseAttachment {
	const FALLBACK = "fallback";
	const COLOR = "color";
	const TITLE = "title";
	const FIELDS = "fields";
	const TEXT = "text";
	const PRETEXT = "pretext";
	const MARKDOWN_IN = "mrkdwn_in";

	const COLOR_ORANGE = "#F37735";
	const COLOR_RED = "#DF1C1C";
	const COLOR_GREEN = "#52BF90";

	private $_fallback;
	private $_title;
	private $_fields;
	private $_text;
	private $_pretext;
	private $_markdownIn;
	private $_color;

	public function __construct() {
		$this->_text = "";
		$this->_markdownIn = array();
		$this->_fields = array();
		return $this;
	}

	public function setFallback($fallback) {
		$this->_fallback = $fallback;
		return $this;
	}

	public function setTitle($title) {
		$this->_title = $title;
		return $this;
	}

	public function addField($field) {
		$this->_fields[] = $field->build();
		$this->addMarkdownIn(self::FIELDS);
		return $this;
	}

	public function addText($text) {
		$this->_text .= $text;
		$this->addMarkdownIn(self::TEXT);
		return $this;
	}

	public function addTextOnNewLine($text) {
		$this->_text .= "\n$text";
		return $this;
	}

	public function setPretext($pretext) {
		$this->_pretext = $pretext;
		$this->addMarkdownIn(self::PRETEXT);
		return $this;
	}

	public function setColor($color) {
		$this->_color = $color;
		return $this;
	}

	public function addMarkdownIn($key) {
		if (!in_array($key, $this->_markdownIn)) {
			$this->_markdownIn[] = $key;
		}
	}

	/**
	 * Convert the `ReponseAttachment` object into an array that Slack understands
	 */
	public function build() {
		return array (
			self::FALLBACK => $this->_fallback,
			self::TITLE => $this->_title,
			self::FIELDS => $this->_fields,
			self::TEXT => $this->_text,
			self::PRETEXT => $this->_pretext,
			self::MARKDOWN_IN => $this->_markdownIn,
			self::COLOR => $this->_color,
		);
	}
}