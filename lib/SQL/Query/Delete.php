<?php
namespace SQL\Query;

use SQL\Query;
use SQL\Util\Condition;
use SQL\Where;

final class Delete extends Query{
  use Where;

  public function getValue(): array{
    $data = [];

    foreach($this->where as $v)
      array_push($data, ...$v->getValue());

    return $data;
  }

  public function __toString(){
    $str = "DELETE FROM $this->table";
    
    if(count($this->where))
      $str.= ' WHERE '.Condition::enclose(...$this->where)->setEnclose(false);

    return $str;
  }
}