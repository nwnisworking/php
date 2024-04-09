<?php
namespace SQL\Drivers;
use Exception;
use PDO;
use SQL\Driver;

final class MySQL extends Driver{
  public function connect(): bool|self{
    $query = ['host'=>@$_ENV['DB_HOST'] ?? 'localhost'];
    $prefix = @$_ENV['DB_PREFIX'] ?? '';
    $user = @$_ENV['DB_USER'] ?? 'root';
    $pass = @$_ENV['DB_PASS'] ?? '';

    if(isset($_ENV['DB_PORT']))
      $query['port'] = $_ENV['DB_PORT'];

    if(isset($_ENV['DB_NAME']))
      $query['dbname'] = $_ENV['DB_NAME'];

    try{
      $this->driver = new PDO(
        $this->getName().':'.http_build_query($query, '', ';'),
        $prefix.$user,
        $pass
      );

      return $this;
    }
    catch(Exception $e){}

    return false;
  }
}