<?php
namespace SQL\Drivers;
use Exception;
use PDO;
use SQL\Driver;

final class MySQL extends Driver{
  public function connect(): bool|self{
    $prefix = @self::$config['DB_PREFIX'] ?? '';
    $host = @self::$config['DB_HOST'] ?? 'localhost';
    $port = @self::$config['DB_PORT'] ?? '';
    $name = @self::$config['DB_NAME'] ?? '';
    $user = @self::$config['DB_USER'] ?? 'root';
    $pass = @self::$config['DB_PASS'] ?? '';
    
    try{
      $this->driver = new PDO(
        $this->getName().":host=$host;port=$port;dbname=$prefix$name",
        $prefix.$user,
        $prefix.$pass
      );

      return $this;
    }
    catch(Exception $e){}

    return false;
  }
}