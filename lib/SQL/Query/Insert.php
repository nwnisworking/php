<?php
namespace SQL\Query;

use SQL\Query;

final class Insert extends Query{
  private array $values = [];

  public function __construct(string $table, ?array $columns = null){
    parent::__construct($table, $columns);

    if(is_array($columns) && array_is_list($columns))
      $this->columns = array_values($columns);
    
    elseif(is_array($columns) && !array_is_list($columns)){
      $this->columns = array_keys($columns);
      $this->values[] = array_values($columns);
    }
    
    else
      $this->columns = null;
  }

  public function setValue(array ...$values): self{
    foreach($values as $v)
      if(is_array($this->columns) && (count($v) === count($this->columns)) || is_null($this->columns))
          $this->values[] = $v;
      else
        continue;

    return $this;
  }

  public function getValue(): array{
    $values = array_merge(...$this->values);
    return $values;
  }

  public function __toString(){
    $str = "INSERT INTO $this->table";

    if(count($this->columns))
      $str.= '('.join(', ', $this->columns).')';

    if(count($this->values))
      $str.= ' VALUES';

    foreach($this->values as $value)
      $str.= '('.join(', ', array_map(fn($e)=>'?', $value)).'), ';

    return rtrim($str, ', ');
  }
}