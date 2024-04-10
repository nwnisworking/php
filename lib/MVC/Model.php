<?php
namespace MVC;
use SQL\Driver;

abstract class Model{
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
}