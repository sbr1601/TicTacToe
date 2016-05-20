<?php

class DBConnection {
	const READ_USER = 'dbReader';
	const READ_USER_PWD = 'r3@d0n1y';
	const WRITE_USER = 'dbWriter';
	const WRITE_USER_PWD = 'r3@dWr1t3';

	const DATABASE_HOST = 'localhost';
	const DATABASE_NAME = 'TicTacToe';

	const INT = 1;
	const TEXT = 2;

	private $_conn;
	private $_db;

	private static $_dbConfig = array (
		'tttRead' => array (
			'host' => self::DATABASE_HOST,
			'database' => self::DATABASE_NAME,
			'user' => self::READ_USER,
			'password' => self::READ_USER_PWD,
		),

		'tttWrite' => array (
			'host' => self::DATABASE_HOST,
			'database' => self::DATABASE_NAME,
			'user' => self::WRITE_USER,
			'password' => self::WRITE_USER_PWD,
		),
	);


	public function __construct($db) {
		if (!isset(self::$_dbConfig[$db])) {
			error_log("Configuration for database=$db does not exist");
			return false;
		}
		$this->_db = $db;
		$this->_setupConn();
	}

	/**
	 * Check if a mySQL conneciton already exists for this instance
	 * If no connection exists, setup a new DB connection
	 * It uses the $_dbConfig variable to determine the connection parameters
	 * @throws Exception if connection to DB fails
	 */
	private function _setupConn() {
		if ($this->_conn !== null) {
			return;
		}

		$config = self::$_dbConfig[$this->_db];
		$this->_conn = new mysqli($config['host'], $config['user'], $config['password'], $config['database']);
		if ($this->_conn->connect_error) {
			throw new Exception("DB Connection Failed: " . $this->_conn->connect_error);
		}
	}

	/**
	 * Query the Database with the passed SQL query
	 * 
	 * @param SQL query to be run on the DB
	 * @return mysqli object with the query results
	 */
	public function query($sql) {
		$this->_setupConn();
		return $this->_conn->query($sql);
	}

	/**
	 * Query the Database with the passed SQL query
	 *
	 * @param SQL query to be run on the DB
	 * @return SQL query results in an array form
	 */
	public function queryToArray($sql) {
		$data = array();
		$result = $this->query($sql);
		if (!$result) {
			return array();
		}

		while($row = $result->fetch_assoc()) {
			$data[] = $row;
		}
		return $data;
	}

	/**
	 * Quote and Validate the value before using it in a query
	 *
	 * @param value to be quoted
	 * @param type of the value
	 * @return if type is INT, integer casts the value before returning it
	 * @return if type is TEXT (or default), escapes the string to prevent SQL Injection attacks
	 */
	public function quote($val, $type = self::TEXT) {
		switch ($type) {
			case self::INT:
				return (int) $val;
			case self::TEXT:
			default:
				return "'" . $this->_conn->real_escape_string($val) . "'";
		}
	}

  public function quoteCSV($arrVals, $type = self::TEXT) {
    $values = "";
    foreach ($arrVals as $val) {
      $values .= "," . $this->quote($val, $type);
    }
    return ltrim($values, ",");
  }
}
