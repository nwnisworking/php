<?php
namespace SQL\Drivers;
use PDO;
use SQL\Driver;

class SQLite extends Driver{
	protected static array $config = [];

	protected static PDO $driver;

	public function connect(): bool|self{
		return false;
	}
}