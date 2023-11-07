<?php

namespace src\Repositories;

require_once __DIR__ . '/../../vendor/autoload.php';

use mysqli;

class Repository {

	protected mysqli $mysqlConnection;

	private string $hostname;
	private string $username;
	private string $databaseName;
	private string $databasePassword;

	public function __construct() {
		// TODO: use https://github.com/vlucas/phpdotenv so we don't have hardcoded credentials here.
		$this->hostname = 'localhost';
		$this->username = 'root';
		$this->databaseName = 'posts_web_app';
		$this->databasePassword = '';

		$this->mysqlConnection = new mysqli($this->hostname, $this->username, $this->databasePassword, $this->databaseName);
		if ($this->mysqlConnection->connect_error) {
			die('Connection failed: ' . $this->mysqlConnection->connect_error);
		}
	}

	public function getRawConnection(): mysqli {
		return $this->mysqlConnection;
	}

}
