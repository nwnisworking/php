<?php
use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator as RII;

final class Autoload{
	private static array $path = [];

	public static function include(string $class): void{
		assert(isset(self::$path[$class]), "Class did not follow file semantic and cannot be referenced as a result");

		include_once self::$path[$class];
	}

	public static function load(string $dir, bool $last = false): void{
		$rii = new RII(new RDI($dir, RDI::SKIP_DOTS), RII::LEAVES_ONLY);

		/** 
		 * This will trim the last directory so that the key will contain the last directory name first
		 */
		if($last)
			$dir = substr($dir, 0, strrpos($dir, '\\'));

		foreach($rii as $file){
			$path = $file->getPathName();
			$class = trim(str_replace('/', '\\', str_replace([$dir, '.php'], '', $path)), '\\/');

			self::$path[$class] = $path;
		}
	}

	public static function getPaths(string $key): array{
		return array_filter(
			self::$path, 
			fn($e)=>str_contains(strtolower($e), strtolower($key)), 
			ARRAY_FILTER_USE_KEY
		);
	}

	public static function get(string $key): ?string{
		foreach(self::$path as $k=>$v)
			if(str_contains(strtolower($k), strtolower($key)))
				return $k;

		return null;
	}
}

Autoload::load(__DIR__);
spl_autoload_register([Autoload::class, 'include']);