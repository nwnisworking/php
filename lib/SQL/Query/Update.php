<?php
namespace SQL\Query;

use InvalidArgumentException;
use SQL\Query;
use SQL\Util\Condition;
use SQL\Where;

final class Update extends Query{
  use Where;
  
  private array $values = [];

  public function __construct(string $table, array $columns){
    if(array_is_list($columns))
      throw new InvalidArgumentException;

    parent::__construct($table, $columns);
    $this->columns = array_keys($columns);
    $this->values = array_values($columns);
  }

  public function getValue(): array{
    $data = array_merge($this->values);

    foreach($this->where as $v)
      array_push($data, ...$v->getValue());

    return $data;
  }

  public function __toString(): string{
    $str = "UPDATE $this->table SET ".join(', ', array_map(fn($e)=>"$e = ?", $this->columns));
    
    if(count($this->where))
      $str.= ' WHERE '.Condition::enclose(...$this->where)->setEnclose(false);

    return $str;
  }
}