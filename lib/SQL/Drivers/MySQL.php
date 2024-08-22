<?php
namespace SQL\Drivers;
use Exception;
use PDO;
use SQL\Driver;

class MySQL extends Driver{
  protected static array $config = [];

  protected static PDO $driver;

  public function connect(): bool|self{
    if(isset(self::$driver))
      return $this;

    try{
      self::$driver = new PDO(
        $this->dsn([
          'host'=>$this->config('host'),
          'port'=>$this->config('port'),
          'dbname'=>$this->config('dbname'),
          'unix_socket'=>$this->config('unix_socket'),
          'charset'=>$this->config('charset')
        ]), 
        $this->config('user'), 
        $this->config('password')
      );
    }
    catch(Exception $err){
      assert(false, $err);
      return false;
    }

    return $this;
  }
}