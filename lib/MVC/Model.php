<?php
namespace MVC;
use JsonSerializable;
use SQL\Driver;

abstract class Model implements JsonSerializable{
  protected static Driver $driver;

  protected static string $table;
}