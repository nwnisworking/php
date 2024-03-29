<?php
final class Validator{
  public function __construct(private array $data){}
  
  public function validate(array $data): bool{
    foreach($this->data as $k=>$v)
      if(!isset($data[$k]) || !$v($data[$k])) return false;
    
    return true;
  }

  public function rule(string $key, callable $rule): self{
    $this->data[$key] = $rule;
    return $this;
  }

  public static function isEmail(): callable{
    return fn($e)=>filter_var($e, FILTER_VALIDATE_EMAIL);
  }

  public static function isBool(): callable{
    return fn($e)=>filter_var($e, FILTER_VALIDATE_BOOLEAN);
  }

  public static function isFloat(?int $min = null, ?int $max = null): callable{
    $opt = [];
    !isset($min) ?: $opt['min_range'] = $min;
    !isset($max) ?: $opt['max_range'] = $max;

    return fn($e)=>($a = filter_var($e, FILTER_VALIDATE_FLOAT, ['options'=>$opt])) ? $a : filter_var($e, FILTER_VALIDATE_FLOAT, ['options'=>['decimal'=>',', ...$opt]]);
  }

  public static function isInt(?int $min = null, ?int $max = null): callable{
    return fn($e)=>filter_var($e, FILTER_VALIDATE_INT, ['options'=>['min_range'=>$min, 'max_range'=>$max]]);
  }

  public static function isRegex(string $regexp): callable{
    return fn($e)=>filter_var($e, FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>$regexp]]);
  }

  public static function isIn(string ...$value): callable{
    return fn($e)=>in_array($e, $value);
  }
}