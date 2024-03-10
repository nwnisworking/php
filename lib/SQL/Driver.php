<?php
namespace SQL;

use PDO;
use SQL\Query\Delete;
use SQL\Query\Insert;
use SQL\Query\Select;
use SQL\Query\Update;

abstract class Driver{
	protected PDO $driver;

	protected Query $query;

	public function getName(): string{
		return strtolower(substr(static::class, strrpos(static::class, '\\') + 1));
	}

	public function select(string $table, ?array $columns = null): Select{
		return $this->query = new Select($table, $columns);
	}

	public function delete(string $table): Delete{
		return $this->query = new Delete($table);
	}

	public function insert(string $table, ?array $columns = null): Insert{
		return $this->query = new Insert($table, $columns);
	}

	public function update(string $table, array $columns): Update{
		return $this->query = new Update($table, $columns);
	}

	public abstract function connect(?array $config = []): bool|self;
}