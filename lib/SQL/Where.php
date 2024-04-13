<?php
namespace SQL;

use SQL\Query\Select;
use SQL\Util\Condition;
use SQL\Util\Symbol;

trait Where{
  protected array $where = [];

  public function where(Condition ...$condition): self{
    array_push($this->where, ...$condition);
    return $this;
  }

  public function bit(string $column, string $op, int|Symbol $value, string $glue = 'AND'): self{
    return $this->where(Condition::bit($column, $op, $value, $glue));
  }

  public function not(Condition $condition): self{
    return $this->where($condition->not());
  }

  public function lt(string|Condition $column, Symbol|string|int $value, string $glue = 'AND'): self{
    return $this->where(Condition::lt($column, $value, $glue));
  }

  public function gt(string|Condition $column, Symbol|string|int $value, string $glue = 'AND'): self{
    return $this->where(Condition::gt($column, $value, $glue));
  }

  public function eq(string|Condition $column, Symbol|string|int $value, string $glue = 'AND'): self{
    return $this->where(Condition::eq($column, $value, $glue));
  }

  public function lte(string|Condition $column, Symbol|string|int $value, string $glue = 'AND'): self{
    return $this->where(Condition::lte($column, $value, $glue));
  }

  public function gte(string|Condition $column, Symbol|string|int $value, string $glue = 'AND'): self{
    return $this->where(Condition::gte($column, $value, $glue));
  }

  public function neq(string|Condition $column, Symbol|string|int $value, string $glue = 'AND'): self{
    return $this->where(Condition::neq($column, $value, $glue));
  }

  public function like(string $column, Symbol|string $value, string $glue = 'AND'): self{
    return $this->where(Condition::like($column, $value, $glue));
  }

	public function in(string $column, array $value, string $glue = 'AND'): self{
    return $this->where(Condition::in($column, $value, $glue));
  }

  public function between(string $column, string|int|Symbol $min, string|int|Symbol $max, string $glue = 'AND'): self{
    return $this->where(Condition::between($column, $min, $max, $glue));
  }

  public function isNull(string|Condition $column, string $glue = 'AND'): self{
    return $this->where(Condition::isNull($column, $glue));
  }

  public function isNotNull(string|Condition $column, string $glue = 'AND'): self{
    return $this->where(Condition::isNotNull($column, $glue));
  }

  public function exists(Select $select, string $glue = 'AND'): self{
    return $this->where(Condition::exists($select, $glue));
  }

  public function notExists(Select $select, string $glue = 'AND'): self{
    return $this->where(Condition::exists($select, $glue)->not());
  }
}