<?php
namespace SQL\Drivers;
use Exception;
use PDO;
use SQL\Driver;

final class MySQL extends Driver{
  public function connect(?array $config = []): bool|self{
    $prefix = @$config['DB_PREFIX'] ?? '';
    $host = @$config['DB_HOST'] ?? 'localhost';
    $port = @$config['DB_PORT'] ?? '';
    $name = @$config['DB_NAME'] ?? '';
    $user = @$config['DB_USER'] ?? 'root';
    $pass = @$config['DB_PASS'] ?? '';
    
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