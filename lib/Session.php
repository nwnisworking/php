<?php
final class Session{
	public static function start(): void{
		if(self::isNone()){
			session_start([
				'cookie_path'=>self::config('path'),
				'cookie_domain'=>self::config('domain'),
				'cookie_samesite'=>self::config('samesite').'; '.self::config('partitioned') ? 'Partitioned' : '',
				'cookie_secure'=>self::config('secure'),
				'cookie_httponly'=>self::config('httponly'),
				'cookie_lifetime'=>self::config('lifetime'),
			]);

			if(is_null(self::get('timestamp')) && !is_null(self::config('ttl')))
				self::set('timestamp', time());

			self::set('locked', false);
		}
	}

	public static function abort(): void{
		self::start();
		session_abort();
	}

	public static function destroy(): void{
		self::start();
		session_destroy();
	}

	public static function empty(): void{
		self::start();
		$_SESSION = [];
	}

	public static function regenerate(callable|array|null $fn = null): void{
		self::start();

		$timestamp = self::get('timestamp');

		if(is_null(self::config('ttl')) || time() - $timestamp > self::config('ttl')){
			$prev_data = self::all();
			self::empty();
			self::set('expired', true);

			session_regenerate_id();
			self::set($prev_data);
			self::set('expired', false);
			self::set('timestamp', time());
		}

		if(self::get('locked')){
			while(self::get('locked'))
				usleep(10000);
		}
		else{
			self::set('locked', true);

			if($fn)
				call_user_func($fn);

			self::set('locked', false);
		}
	}

	public static function getId(): string{
		return session_id();
	}

	public static function getName(): string{
		return session_name();
	}

	public static function isDisabled(): bool{
		return session_status() === PHP_SESSION_DISABLED;
	}

	public static function isNone(): bool{
		return session_status() === PHP_SESSION_NONE;
	}

	public static function isActive(): bool{
		return session_status() === PHP_SESSION_ACTIVE;
	}

	public static function all(): array{
		return $_SESSION;
	}

	public static function get(string $key): mixed{
		return @$_SESSION[$key];
	}

	public static function set(string|array $key, mixed $value = null): void{
		if(is_array($key))
			$_SESSION = [...$_SESSION, ...$key];
		else
			$_SESSION[$key] = $value;
	}

	public static function config(string $key, mixed $value = null): mixed{
		static $config = null;

		if(is_null($config)){
			$config = [
				'name'=>'PHPSESSID',
				'path'=>'/',
				'domain'=>'localhost',
				'samesite'=>'None',
				'partitioned'=>false,
				'secure'=>false,
				'httponly'=>false,
				'lifetime'=>0,
				'ttl'=>null
			];
		}

		if(isset($value))
			$config[$key] = $value;

		return @$config[$key];
	}
}