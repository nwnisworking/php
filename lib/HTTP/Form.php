<?php
namespace HTTP;
use Closure;
use Exception;

abstract class Form{
	public function __construct(private Request $request){}

	public function validate(array &$result): bool{
		$is_valid = true;

		foreach($this->rules() as $name=>$rules){
			$value = $this->request->post($name);

			foreach($rules as $rule){
				$rule = call_user_func($rule, $value);

				if($rule === true)
					continue;

				if(!isset($result[$name]))
					$result[$name] = [];

				$result[$name][] = $rule;
				$is_valid = false;
			}
		}

		return $is_valid;
	}

	public static function rule(string $fn, int $status, string $message): Closure{
		if($pos = strpos($fn, ':')){
			$name = substr($fn, 0, $pos);
			$param = substr($fn, $pos + 1);

			if($name !== 'regexp')
				$param = [$param];
			else
				$param = preg_split('/,/', $param);
		}
		else{
			$name = $fn;
			$param = [];
		}

		return fn(mixed $value)=>call_user_func([self::class, $name], $value, ...$param) ? true : ['message'=>$message, 'status'=>$status];
	}

	private static function required(mixed $value): bool{
    return isset($value);
  }

  private static function json(mixed $value): bool{
    try{
      json_decode($value);
      return true;
    }
    catch(Exception $err){return false;}
  }

  private static function bool(mixed $value): bool{
    return in_array($value, [true, false, 1, 0, '1', '0']);
  }

  private static function array(mixed $value): bool{
    return is_array($value);
  }

  private static function regexp(mixed $value, string $regexp): bool{
    return filter_var($value, FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>$regexp]]);
  }

  private static function email(string $value): bool{
    return filter_var($value, FILTER_VALIDATE_EMAIL);
  }

  private static function in(string $value, array $match = []): bool{
    return in_array($value, $match);
  }

  private static function range(int $value, int $min, ?int $max = null): bool{
    $max??= $min;
    return $value >= $min && $value <= $max;
  }

  private static function len(string $value, int $min, ?int $max = null): bool{
    $max??= $min;
    $value = strlen($value);
    return $value >= $min && $value <= $max;
  }

  public abstract function rules(): array;
}