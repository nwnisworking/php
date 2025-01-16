<?php
namespace Web;

/**
 * Class Session
 * A utility class for managing PHP sessions.
 */
class Session {
	/**
	 * Starts a session if not already started.
	 *
	 * @return void
	 */
	public static function start(): void {
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
	}

	/**
	 * Checks if a session is active.
	 *
	 * @return bool True if a session is active, false otherwise.
	 */
	public static function isActive(): bool {
		return session_status() === PHP_SESSION_ACTIVE;
	}

	/**
	 * Sets a value in the session.
	 *
	 * @param string|array $key The key to set.
	 * @param mixed $value The value to store.
	 * @return void
	 */
	public static function set(string $key, mixed $value): void {
		self::start();
		if(is_array($key)){
			$_SESSION = array_merge($_SESSION, $key);
		}
		else{
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * Gets a value from the session.
	 *
	 * @param string $key The key to retrieve.
	 * @param mixed|null $default The default value to return if the key is not set.
	 * @return mixed The value from the session or the default value.
	 */
	public static function get(string $key, mixed $default = null): mixed {
		self::start();
		return $_SESSION[$key] ?? $default;
	}

	/**
	 * Checks if a key exists in the session.
	 *
	 * @param string $key The key to check.
	 * @return bool True if the key exists, false otherwise.
	 */
	public static function has(string $key): bool {
		self::start();
		return isset($_SESSION[$key]);
	}

	/**
	 * Removes a key from the session.
	 *
	 * @param string $key The key to remove.
	 * @return void
	 */
	public static function remove(string $key): void {
		self::start();
		unset($_SESSION[$key]);
	}

	/**
	 * Clears all session data.
	 *
	 * @return void
	 */
	public static function clear(): void {
		self::start();
		$_SESSION = [];
	}

	/**
	 * Destroys the session.
	 *
	 * @return void
	 */
	public static function destroy(): void {
		if (self::isActive()) {
			session_unset();
			session_destroy();
		}
	}

	/**
	 * Regenerates the session ID to prevent session fixation attacks.
	 *
	 * @param bool $deleteOldSession Whether to delete the old session data.
	 * @return void
	 */
	public static function regenerate(bool $delete_old_session = true): void {
		self::start();
		session_regenerate_id($delete_old_session);
	}

	/**
	 * Sets session cookie parameters.
	 *
	 * @param int $lifetime The lifetime of the session cookie in seconds.
	 * @param string $path The path on the server where the cookie is available.
	 * @param string $domain The domain for which the cookie is available.
	 * @param bool $secure Whether the cookie should only be sent over HTTPS.
	 * @param bool $httpOnly Whether the cookie is accessible only through HTTP protocol.
	 * @return void
	 */
	public static function setCookieParams(
		string $name,
		int $lifetime = 0,
		string $path = '/',
		string $domain = '',
		bool $secure = false,
		bool $httpOnly = true
	): void {
		session_name($name);
		session_set_cookie_params([
			'lifetime' => $lifetime,
			'path' => $path,
			'domain' => $domain,
			'secure' => $secure,
			'httponly' => $httpOnly,
		]);
	}

	/**
	 * Retrieves the current session ID.
	 *
	 * @return string The session ID.
	 */
	public static function getId(): string {
			self::start();
			return session_id();
	}

	/**
	 * Sets the session ID.
	 *
	 * @param string $id The session ID to set.
	 * @return void
	 */
	public static function setId(string $id): void {
			if (session_status() !== PHP_SESSION_ACTIVE) {
					session_id($id);
			}
	}
}
