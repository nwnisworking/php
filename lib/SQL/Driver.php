<?php
namespace SQL;

use Exception;
use PDO;
use PDOStatement;
use SQL\Query\Delete;
use SQL\Query\Insert;
use SQL\Query\Select;
use SQL\Query\Update;

abstract class Driver{
	protected PDO $driver;

	protected Query $query;

	protected static array $config;
	
	public function getName(): string{
		return strtolower(substr(static::class, strrpos(static::class, '\\') + 1));
	}

	public function select(string $table, ?array $columns = ['*']): Select{
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

	private function execute(): bool|PDOStatement{
		$prepare = $this->driver->prepare($this->query);

		if(!$prepare->execute($this->query->getValue()))
			return false;

		return $prepare;
	}

	public function fetchObj(): ?array{
		return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_OBJ);
	}

	public function fetchColumn(int $index): ?array{
		return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_COLUMN, $index);
	}

	public function fetchAssoc(): ?array{
		return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_ASSOC);
	}

	public function fetchClass(string $class): ?array{
		return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_CLASS, $class);
	}

	public function fetchFunc(callable $fn){
		return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_FUNC, $fn);
	}

	public abstract function connect(): bool|self;

	public static function init(?array $config = []): void{
		self::$config = $config;
	}
}