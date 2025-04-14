<?php

use RecursiveDirectoryIterator as RDirectoryIterator;
use RecursiveIteratorIterator as RIteratorIterator;

/**
 * Final Class Autoload
 *
 * Manages class autoloading by mapping class names to their respective file paths.
 */
final class Autoload{
	/**
	 * List of available classes path.
	 * 
	 * @var array<string, string>
	 */
	private static array $classes = [];

	/**
	 * Identifies whether autoloader is registered
	 * 
	 * @var bool
	 */
	private static bool $has_autoload_register = false;

	/**
	 * Loads all PHP files within the specified directory into the autoload map.
	 *
	 * @param string $dir The directory to scan for PHP files.
	 * @param bool $first_segment Whether to preserve the first segment of the namespace.
	 *
	 * @return void
	 */
	public static function load(string $dir, bool $first_segment = false): void{
		$files = new RIteratorIterator(new RDirectoryIterator($dir, RDirectoryIterator::SKIP_DOTS));

		foreach($files as $file){
			$path = $file->getPathName();
			$class = str_replace(['/', '.php', !$first_segment ? "$dir\\" : ""], ['\\', ''], $path);

			self::$classes[$class] = $path;
		}
	}

	/**
	 * Retrieves the file path associated with a given class name.
	 *
	 * @param string $class The fully qualified class name.
	 *
	 * @return string|null The file path if found, or null otherwise.
	 */
	public static function getPath(string $class): ?string{
		return @self::$classes[$class];
	}

	/**
	 * Checks if a class exists in the autoload map.
	 *
	 * @param string $class The fully qualified class name.
	 *
	 * @return bool True if the class exists, false otherwise.
	 */
	public static function exists(string $class): bool{
		return array_key_exists($class, self::$classes);
	}

	/**
	 * Initializes the autoloader and registers it with SPL.
	 *
	 * @param bool $trigger Trigger to register or unregister the autoloader
	 */
	public static function start(bool $trigger = true): void{
		if($trigger && self::$has_autoload_register || !$trigger && !self::$has_autoload_register) return;

		if($trigger)
			spl_autoload_register([self::class, 'loader']);
		else
			spl_autoload_unregister([self::class,'loader']);

		self::$has_autoload_register = $trigger;
	}

	/**
	 * A callback function designated for loading classes 
	 * 
	 * @throws RuntimeException If the requested class cannot be found in the map.
	 * 
	 * @return void
	 */
	private static function loader(string $class): void{
		if(!self::exists($class))
			throw new RuntimeException("Unable to load class $class");

		include_once self::getPath($class);
	}
}
