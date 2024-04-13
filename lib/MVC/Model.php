<?php
namespace MVC;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;
use SQL\Driver;
use SQL\Ignore;

abstract class Model implements JsonSerializable{
  protected Driver $driver;

  public static string $table;

  public function __construct(){
    $this->driver = Driver::create();
    $this->driver->connect();
  }

  public function __serialize(){
    unset($this->driver);
    return (array) $this;
  }

  public function __wakeup(){
    $this->driver = Driver::create();
    $this->driver->connect();
  }

  public function jsonSerialize(){
    $rc = new ReflectionClass($this);
    $data = [];

    foreach($rc->getProperties(ReflectionProperty::IS_PUBLIC) as $prop){
      if(!$prop->getAttributes(Ignore::class) && !$prop->isStatic())
        $data[$prop->getName()] = $prop->getValue($this);
      continue;
    }

    return $data;
  }

  /**
   * @return static[]
   */
  public static function get(array $kv): array{
    $driver = Driver::create();
    $query = $driver->select(static::$table);


    foreach($kv as $k=>$v)
      $query->in($k, is_array($v) ? $v : [$v]);
    
    return $driver->fetchClass(static::class);
  }

  public static function exists(array $kv): mixed{
    $driver = Driver::create();
    $query = $driver->select(static::$table, ['COUNT(*)']);

    foreach($kv as $k=>$v)
      $query->in($k, is_array($v) ? $v : [$v]);

    return !!($driver->fetchColumn(0)[0]);
  }
}