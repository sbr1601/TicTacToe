<?php

class Response {
	const RESPONSE_TYPE = "response_type";
	const ATTACHMENTS = "attachments";
	const TEXT = "text";
	const MARKDOWN = "mrkdwn";

	const IN_CHANNEL = "in_channel";
	const EPHEMERAL = "ephemeral";

	const EMOJI_GRIN = ":grinning:";
	const EMOJI_DISAPPOINTED = ":disappointed:";

	private $_responseType;
	private $_text;
	private $_attachments;
	private $_markdown;

	public function __construct() {
		$this->_markdown = true;
		$this->_responseType = self::IN_CHANNEL;
		$this->_attachments = array();
		return $this;
	}

	public function setResponseType($response_type) {
		$this->_responseType = $response_type;
		return $this;
	}

	public function setText($text) {
		$this->_text = $text;
		return $this;
	}

	public function addAttachment($attachment) {
		$this->_attachments[] = $attachment->build();
		return $this;
	}

	/**
	 * Adds a link to the user_name in Slack
	 *
	 * @param user - user_name of the user who needs to be linked
	 * @return linked user_name
	 */
	public static function linkUser($user) {
		return "<@$user>";
	}

	/**
	 * Make the text bold in Slack
	 *
	 * @param text - that needs to be bolded
	 * @return bolded text
	 */
	public static function boldText($text) {
		return "*$text*";
	}

	/**
	 * Format the text as a code in Slack
	 *
	 * @param text - that needs to be formatted as code
	 * @return formatted text
	 */
	public static function codeText($text) {
		return "`$text`";
	}

	/**
	 * Let Slack know that the input text has been preformatted
	 *
	 * @param text - that is preformatted
	 * @return text that slack will preserve formatting on
	 */
	public static function preformatText($text) {
		return "```$text```";
	}

	/**
	 * Convert the `Reponse` object into an array that Slack understands
	 */
	public function build($json = true) {
		$message = array (
			self::RESPONSE_TYPE => $this->_responseType,
			self::TEXT => $this->_text,
			self::ATTACHMENTS => $this->_attachments,
			self::MARKDOWN => $this->_markdown,
		);

		return ($json) ? json_encode($message) : $message;
	}

	public static function getExceptionMessage($ex) {
		return (
			(new Response())
			->setResponseType(self::EPHEMERAL)
			->setText($ex->getMessage())
			->build()
		);
	}
}
