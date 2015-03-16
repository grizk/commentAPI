<?php

class Database extends PDO {

	public function __construct() {
		parent::__construct('mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME, Config::DB_USER, Config::DB_PASSWORD);
		$this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
}
