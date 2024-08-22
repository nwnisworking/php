<?php
final class Env{
	public static function get(string $key): ?string{
		return @$_ENV[$key];
	}

	public static function set(string $key, int|string $value): void{
		$_ENV[$key] = $value;
	}

	public static function load(string $url): void{
		foreach(parse_ini_file($url) as $k=>$v)
			self::set($k, $v);
	}
}