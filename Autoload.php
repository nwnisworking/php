<?php
use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator as RII;

/**
 * Class Autoload
 * A custom autoloader for dynamically including PHP classes based on a directory structure.
 */
final class Autoload {
	/**
	 * @var array $path Maps class names to their respective file paths.
	 */
	private static array $path = [];

	/**
	 * Includes the file corresponding to the given class name.
	 *
	 * @param string $class The fully qualified class name to include.
	 * @throws AssertionError If the class is not found in the `$path` array.
	 */
	public static function include(string $class): void {
		assert(
			isset(self::$path[$class]),
			"Class $class did not follow file semantic and cannot be referenced as a result"
		);

		include_once self::$path[$class];
	}

	/**
	 * Scans a directory recursively and maps class names to file paths.
	 *
	 * @param string $dir The directory to scan.
	 * @param bool $last If true, trims the last directory from the path for key generation.
	 */
	public static function load(string $dir, bool $last = false): void {
		$rii = new RII(new RDI($dir, RDI::SKIP_DOTS), RII::LEAVES_ONLY);

		// Adjust base directory for class key generation if $last is true.
		if ($last) {
			$dir = substr($dir, 0, strrpos($dir, '\\'));
		}

		foreach ($rii as $file) {
			$path = $file->getPathName();
			$class = trim(
				str_replace('/', '\\', str_replace([$dir, '.php'], '', $path)),
				'\\/'
			);

			self::$path[$class] = $path;
		}
	}

	/**
	 * Retrieves all paths matching a specific key.
	 *
	 * @param string $key The search key (case-insensitive).
	 * @return array An array of paths where the keys match the search key.
	 */
	public static function getPaths(string $key): array {
		return array_filter(
			self::$path,
			fn($e) => str_contains(strtolower($e), strtolower($key)),
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * Retrieves the first matching class name for a given key.
	 *
	 * @param string $key The search key (case-insensitive).
	 * @return string|null The first matching class name or null if none found.
	 */
	public static function get(string $key): ?string {
		foreach (self::$path as $k => $v) {
			if (str_contains(strtolower($k), strtolower($key))) {
				return $k;
			}
		}

		return null;
	}
}

// Initialize the autoloader and register it with SPL.
Autoload::load(__DIR__);
spl_autoload_register([Autoload::class, 'include']);