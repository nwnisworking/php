<?php
namespace SQL\Util;

use InvalidArgumentException;
use SQL\Query\Select;

class Condition{
  public readonly string|null|Condition $column;

  public readonly ?string $op;
  
  public readonly array|int|string|self|Symbol|Select|null $value;
  
  private ?string $glue = 'AND';

  private bool $is_not = false;

  private bool $is_enclosed = false;

  public function __construct(
    null|string|self $column = null, 
    ?string $op = null, 
    array|int|string|self|Symbol|Select|null $value, 
    ?string $glue = 'AND'
  ){
    $this->column = $column;
    $this->op = $op;
    $this->value = $value;
    $this->glue = $glue;
  }

  public function not(): self{
    $this->is_not^=1;
    return $this;
  }

  public function and(): self{
    $this->glue = 'AND';
    return $this;
  }

  public function or(): self{
    $this->glue = 'OR';
    return $this;
  }

  public function setEnclose(bool $value): self{
    $this->is_enclosed = $value;
    return $this;
  }

  public function getEnclose(): bool{
    return $this->is_enclosed;
  }

  public function getValue(): array{
    $values = [];

    if($this->column && is_a($this->column, self::class))
      array_push($values, ...$this->column->getValue());

    if(is_null($this->value) ||  Symbol::is_symbol($this->value))
      return $values;

    if(is_a($this->value, Select::class))
      return $this->value->getValue();

    if(!is_array($this->value))
      $values[] = $this->value;
    else
      foreach($this->value as $value)
        if(Symbol::is_symbol($value))
          continue;
        elseif(is_a($value, self::class))
          array_push($values, ...$value->getValue());
        else
          $values[] = $value;

    return $values;
  }

  public static function bit(string $column, string $op, int|Symbol $value, string $glue = 'AND'): self{
    $bitwise_op = ['&', '|', '^', '<<', '>>', '~', '&=', '^-=', '|*='];

    if(!in_array($op, $bitwise_op))
      throw new InvalidArgumentException;

    return new self($column, $op, $value, $glue);
  }

  public static function lt(string|self $column, Symbol|int|string|null $value, string $glue = 'AND'): self{
    return new self($column, '<', $value, $glue);
  }

  public static function gt(string|self $column, Symbol|int|string|null $value, string $glue = 'AND'): self{
    return new self($column, '>', $value, $glue);
  }

  public static function eq(string|self $column, Symbol|int|string|null $value, string $glue = 'AND'): self{
    return new self($column, '=', $value, $glue);
  }

  public static function lte(string|self $column, Symbol|int|string|null $value, string $glue = 'AND'): self{
    return new self($column, '<=', $value, $glue);
  }

  public static function gte(string|self $column, Symbol|int|string|null $value, string $glue = 'AND'): self{
    return new self($column, '>=', $value, $glue);
  }

  public static function neq(string|self $column, Symbol|int|string|null $value, string $glue = 'AND'): self{
    return new self($column, '<>', $value, $glue);
  }

  public static function like(string $column, Symbol|string $value, string $glue = 'AND'): self{
    return new self($column, 'LIKE', $value, $glue);
  }

  public static function in(string $column, array $value, string $glue = 'AND'): self{
    return new self($column, 'IN', $value, $glue);
  }

  public static function between(string $column, string|int|Symbol $min, string|int|Symbol $max, string $glue = 'AND'): self{
    return new self($column, 'BETWEEN', [$min, $max], $glue);
  }

  public static function isNull(string $column, string $glue = 'AND'): self{
    return new self($column, 'IS NULL', null, $glue);
  }

  public static function isNotNull(string $column, string $glue = 'AND'): self{
    return new self($column, 'IS NOT NULL', null, $glue);
  }

  public static function any(string $column, Select $select){
    return new self($column, 'ANY', $select->setEnclose(true));
  }

  public static function enclose(self ...$condition): self{
    return (new self(null, null, $condition))->setEnclose(true);
  }

  public static function exists(Select $select, string $glue = 'AND'): self{
    return (new self('', 'EXISTS', $select,));
  }

  private function map(Symbol|string|int|null|Select $e){
    if(Symbol::is_symbol($e))
      return Symbol::key($e);
    elseif(is_null($e))
      return '';
    elseif(is_a($e, Select::class))
      return $e;
    else
      return '?';
  }

  public function __toString(){
    $not = $this->is_not;
    $op = $this->op;
    $value = array_map([$this, 'map'], is_array($this->value) ? $this->value : [$this->value]);

    if(in_array($this->op, ['IN', 'BETWEEN', 'LIKE']))
      $str = $this->column.($not ? ' NOT ' : ' ').$op;
    else{
      $str = trim(($not ? 'NOT' : '').(empty($this->column) ? '' : ' ').$this->column.' '.$op);
    }

    $str.= !strlen($str) ? '' : ' ';

    switch($op){
      case 'IN' : $str.= '('.join(', ', $value).')'; break;
      case 'BETWEEN' : $str.= "$value[0] AND $value[1]"; break;
      case null : 
        foreach($this->value as $i=>$v)
          if(is_a($v, self::class))
            $str.= $v.($i < count($this->value) - 1 ? " $v->glue " : '');
        break;
      case 'EXISTS' : 
        $value[0]->setEnclose(true);
        $this->setEnclose(false);
      default : 
        $str.= $value[0];
    }

    $str = trim($str);
    return sprintf("%s$str%s", !$this->is_enclosed ? '' : '(', !$this->is_enclosed ? '' : ')');
  }
}