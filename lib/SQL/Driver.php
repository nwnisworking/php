<?php
namespace SQL;
use PDO;
use PDOStatement;

abstract class Driver{
	protected static array $config;

	protected static PDO $driver;

	public static function config(?string $key = null, mixed $value = null): mixed{
    if(is_null($key))
      return static::$config;

    else if(is_null($value))
      return @static::$config[$key];

    static::$config[$key] = $value;

    return null;
  }

  protected function dsn(array $config): string{
    $config = array_filter($config, fn($e)=>!empty($e));
    return $this->name().':'.http_build_query($config, '', ';');
  }

  public function name(): string{
    $name = static::class;
    return strtolower(substr($name, strrpos($name, '\\') + 1));
  }

  public abstract function connect(): bool|self;
}

// use Exception;
// use PDO;
// use PDOStatement;
// use SQL\Query\Delete;
// use SQL\Query\Insert;
// use SQL\Query\Select;
// use SQL\Query\Update;

// abstract class Driver{
// 	protected PDO $pdo;

// 	protected Query $query;

// 	public function getName(): string{
// 		return strtolower(substr(static::class, strrpos(static::class, '\\') + 1));
// 	}

// 	public function select(string $table, ?array $columns = ['*']): Select{
// 		return $this->query = new Select($table, $columns);
// 	}

// 	public function delete(string $table): Delete{
// 		return $this->query = new Delete($table);
// 	}

// 	public function insert(string $table, ?array $columns = null): Insert{
// 		return $this->query = new Insert($table, $columns);
// 	}

// 	public function update(string $table, array $columns): Update{
// 		return $this->query = new Update($table, $columns);
// 	}

// 	private function execute(): bool|PDOStatement{
// 		$prepare = $this->pdo->prepare($this->query);

// 		if(!$prepare->execute($this->query->getValue()))
// 			return false;

// 		return $prepare;
// 	}

// 	public function fetchObj(): ?array{
// 		return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_OBJ);
// 	}

// 	public function fetchColumn(int $index): ?array{
// 		return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_COLUMN, $index);
// 	}

// 	public function fetchAssoc(): ?array{
// 		return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_ASSOC);
// 	}

// 	public function fetchClass(string $class): ?array{
// 		return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_CLASS, $class);
// 	}

// 	public function fetchFunc(callable $fn){
// 		return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_FUNC, $fn);
// 	}

// 	public function lastId(): string|bool{
// 		return $this->pdo->lastInsertId();
// 	}

// 	public abstract function connect(): bool|self;

// 	public static function create(): static{
// 		return new ('SQL\\Drivers\\'.(@$_ENV['DB_TYPE'] ?? 'MySQL'));
// 	}
// }