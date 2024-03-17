<?php
/**
 * Todo: Add regnerate id and study about session fixation
 */

final class Session{
	public static function init(array $config = []): void{
		ini_set('session.use_strict_mode', true);
		ini_set('session.name', @$config['SESS_NAME'] ?? 'PHPSESSID');
		session_set_cookie_params([
			'lifetime'=>@$config['SESS_LIFETIME'] ?? 0,
			'path'=>@$config['SESS_PATH'] ?? '/',
			'domain'=>@$config['SESS_DOMAIN'] ?? '',
			'secure'=>@$config['SESS_SECURE'] ?? false,
			'httponly'=>@$config['SESS_HTTPONLY'] ?? false,
			'samesite'=>@$config['SESS_SAMESITE'] ?? ''
		]);
	}

	public static function start(): void{
		if(self::isNone())
			session_start();
	}

	public static function abort(): void{
		if(self::isActive())
			session_abort();
	}

	public static function destroy(): void{
		if(self::isActive())
			session_destroy();
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
}