<?php
namespace SQL\Util;

use LogicException;

class Symbol{
  private string $name;

  private function __construct(?string $name = null){
    $this->name = ($name ?? 'symbol@anonymous').'#'.spl_object_id($this);
  }

  public static function create(?string $name = null){
    return new self($name);
  }

  public static function key(Symbol $symbol){
    return substr($symbol->name, 0, strrpos($symbol->name, '#'));
  }

  public static function is_symbol(mixed $value): bool{
    return $value instanceof Symbol;
  }

  public function __serialize(){
    throw new LogicException(sprintf('Serialization of %s is not allowed', __CLASS__));
  }

  public function __wakeup(){
    throw new LogicException(sprintf('Deserialization of %s is not allowed', __CLASS__));
  }

  public function __clone(){
    throw new LogicException(sprintf('Clone of %s is not allowed', __CLASS__));
  }
}