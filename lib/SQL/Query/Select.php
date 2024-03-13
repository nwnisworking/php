<?php
namespace SQL\Query;
use InvalidArgumentException;
use SQL\Exceptions\JoinTypeException;
use SQL\Query;
use SQL\Util\Condition;
use SQL\Where;

final class Select extends Query{
  use Where;
  
  public const JOIN_TYPE = ['INNER', 'FULL', 'LEFT', 'RIGHT', 'CROSS'];

  public const ORDER_TYPE = ['DESC', 'ASC'];

  private array $join = [];

  private array $having = [];

	private array $group = [];

  private array $order = [];

  private ?int $limit = null;

  private ?int $offset = null;

  private bool $is_enclosed = false;

  private bool $is_distinct = false;

  private bool $is_all = false;

  private string $with;

  public function setDistinct(bool $value): self{
    $this->is_distinct = $value;
    return $this;
  }

  public function getDistinct(): bool{
    return $this->is_distinct;
  }

  public function setEnclose(bool $value): self{
    $this->is_enclosed = $value;
    return $this;
  }

  public function getEnclose(): bool{
    return $this->is_enclosed;
  }

  public function setAll(bool $value): self{
    $this->is_all = $value;
    return $this;
  }

  public function getAll(): bool{
    return $this->is_all;
  }

  public function join(string $type, string $table, ?Condition $condition): self{
    if(!in_array($type = strtoupper($type), self::JOIN_TYPE))
      throw new JoinTypeException;

    if(!isset($this->join[$table]))
      $this->join[$table] = ['type'=>$type, 'condition'=>[$condition]];
    else
      $this->join[$table]['condition'][] = $condition;

    return $this;
  }

  public function innerJoin(string $table, Condition $condition): self{
    return $this->join('INNER', $table, $condition);
  }

  public function leftJoin(string $table, Condition $condition): self{
    return $this->join('LEFT', $table, $condition);
  }

  public function rightJoin(string $table, Condition $condition): self{
    return $this->join('RIGHT', $table, $condition);
  }

  public function fullJoin(string $table, Condition $condition): self{
    return $this->join('FULL', $table, $condition);
  }

  public function crossJoin(string $table): self{
    return $this->join('CROSS', $table, null);
  }

  public function group(string ...$columns): self{
    array_push($this->group, ...$columns);
    return $this;
  }

  public function having(Condition ...$condition): self{
    array_push($this->having, ...$condition);
    return $this;
  }

  public function order(string $column, string $order = 'DESC'): self{
    if(!in_array($order = strtoupper($order), self::ORDER_TYPE))
      throw new InvalidArgumentException;

    array_push($this->order, "$column $order");
    return $this;
  }

  public function limit(int $limit, ?int $offset = null): self{
    $this->limit = $limit;
    $this->offset = $offset;
    return $this;
  }
  
  public function with(string $table): self{
    $this->with = $table;
    return $this;
  }

  public function getValue(): array{
    $data = [];

    foreach(array_merge($this->where, $this->having) as $v)
      array_push($data, ...$v->getValue());  

    return $data;
  }

  public function __toString(){
    $str = "SELECT ";

    if($this->is_distinct)
      $str.= "DISTINCT ";

    if($this->is_all)
      $str.= 'ALL ';

    $str.= join(', ', $this->columns).' ';

    if(!isset($this->table) || empty($this->table))
      return $str;
    else
      $str.= "FROM $this->table";

    if(count($this->join))
      foreach ($this->join as $table=>$join)
        $str.= " $join[type] JOIN $table ON ".Condition::enclose(...$join['condition'])->setEnclose(false);
      
    if(count($this->where))
      $str.= ' WHERE '.Condition::enclose(...$this->where)->setEnclose(false);

    if(count($this->group))
      $str.= ' GROUP BY '.join(',', array_unique($this->group));

    if(count($this->having))
      $str.= ' HAVING '.Condition::enclose(...$this->having)->setEnclose(false);

    if(count($this->order))
      $str.= ' ORDER BY '.join(',', $this->order);

    if(isset($this->limit))
      $str.= " LIMIT $this->limit";

    if(isset($this->offset))
      $str.= " OFFSET $this->offset";

    if(isset($this->with))
      return "WITH $this->with AS ($str)";

    $str = trim($str);
    
    if($this->is_enclosed)
      return "($str)";

    return $str;
  }
}