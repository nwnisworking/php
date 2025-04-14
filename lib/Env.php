<?php

/**
 * Class Env
 *
 * Responsible for loading environment variables from a `.env` file
 * into the `$_ENV` superglobal.
 */
final class Env{
	/**
	 * Loads environment variables from a specified `.env` file.
	 *
	 * @param string $path The path to the `.env` file.
	 *
	 * @throws RuntimeException If the specified file is not readable or parsing fails.
	 *
	 * @return void
	 */
	public static function load(string $path): void{
		if(!is_readable($path))
			throw new RuntimeException("Unable to read .env file at '$path'");

		$result = parse_ini_file($path);

		if(!$result)
			throw new RuntimeException("Failed to parse .env file at '$path'");
		
		foreach($result as $key=>$val)
			$_ENV[$key] = $val;
	}
}
