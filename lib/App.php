<?php
class App{
	public static self $instance;

	private static array $config = [];

	public function __construct(?string $config = null, ?string $route = null){
		self::$instance = $this;
		
		if(is_string($config))
			foreach(scandir($config) as $conf){
				if(str_starts_with($conf, '.')) continue;

				$name = pathinfo($conf, PATHINFO_FILENAME);
				self::$config+= $this->flatten([$name=>include_once $config.DS.$conf]);
			}

		if(is_string($route))
			include_once $route;
	}

	public function config(string $key = null, mixed $value = null): mixed{
		if(!isset($key, $value))
			return self::$config;

		if(isset($value))
			self::$config[$key] = $value;

		return self::$config[$key];
	}

	private function flatten(array $arr, string $prefix = ''): array{
    $result = [];

    foreach($arr as $k=>$v){
      $nk = $prefix === '' ? $k : "$prefix.$k";

      if(is_array($v))
        $result = array_merge($result, $this->flatten($v, $nk));
      else
        $result[$nk] = $v;
    }

    return $result;
  }
}