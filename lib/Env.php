<?php
final class Env{
	public static function init(string $path): void{
		foreach(parse_ini_file($path) as $k=>$v)
			$_ENV[$k] = $v;
	}

	public static function get(string $key): ?string{
		return @$_ENV[$key];
	}

	public static function set(string $key, int|string $value): void{
		$_ENV[$key] = $value;
	}
}