<?php
use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator as RII;

final class Autoload{
  private static array $path = [];

	public static function load(string $dir, ?callable $fn = null){
    $rii = new RII(new RDI($dir, RDI::SKIP_DOTS), RII::LEAVES_ONLY);

		foreach($rii as $f){
			$path = $f->getPathName();
			$class = ltrim(str_replace([$dir, '.php'], '', $path), "\\/");

			// This is to counter conversion of slash from local to production host
			if(is_null($fn) || call_user_func($fn, $class, $path))
				self::$path[str_replace('/', '\\', $class)] = $path; 
		}
	}

	public static function init(){
		spl_autoload_register(fn($e)=>!isset(self::$path[$e]) ?: include_once(self::$path[$e]));
	}

	public static function filterKey(string $key): array{
		return array_filter(self::$path, fn($e)=>str_contains($e, $key));
	}

	public static function filterValue(string $path): array{
		return array_filter(self::$path, fn($e)=>str_contains($e, $path));
	}
}