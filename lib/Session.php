<?php
/**
 * Final class Session
 * 
 * Provides a static interface for managing PHP sessions,
 * including configuration and manipulation of session data.
 */
final class Session{
	/**
	 * Determines if the session status is none.
	 * 
	 * @return bool True if no session exists, otherwise false.
	 */
	public static function isNone(): bool{
		return session_status() == PHP_SESSION_NONE;
	}

	/**
	 * Determines if the session is active.
	 * 
	 * @return bool True if session is active, otherwise false.
	 */
	public static function isActive(): bool{
		return session_status() == PHP_SESSION_ACTIVE;
	}

	/**
	 * Determines if the session is disabled.
	 * 
	 * @return bool True if sessions are disabled, otherwise false.
	 */
	public static function isDisabled(): bool{
		return session_status() == PHP_SESSION_DISABLED;
	}

	/**
	 * Sets the session ID.
	 * 
	 * @param string $id The session ID to set.
	 * 
	 * @throws RuntimeException If the session has already started.
	 */
	public static function setId(string $id): void{
		if(self::isNone())
			session_id($id);
		else
			throw new RuntimeException("Cannot set session ID after the session has already started");
	}

	/**
	 * Gets the current session ID.
	 * 
	 * @return string|false The session ID, or false on failure.
	 */
	public static function getId(): string | false{
		return session_id();
	}

	/**
	 * Sets the session name.
	 * 
	 * @param string $name The session name. Defaults to 'PHPSESSID'.
	 * 
	 * @throws RuntimeException If the session has already started.
	 */
	public static function setName(string $name = 'PHPSESSID'): void{
		if(self::isNone())
			session_name($name);
		else
			throw new RuntimeException("Cannot set session name after the session has already started");
	}

	/**
	 * Gets the current session name.
	 * 
	 * @return string|false The session name, or false on failure.
	 */
	public static function getName(): string | false{
		return session_name();
	}

	/**
	 * Sets the session cookie path.
	 * 
	 * @param string $path The path on the server. Defaults to '/'.
	 * 
	 * @throws RuntimeException If the session has already started.
	 */
	public static function setPath(string $path = '/'): void{
		if(self::isNone())
			session_set_cookie_params(['path'=>$path]);
		else
			throw new RuntimeException("Cannot set session cookie path after the session has already started");
	}

	/**
	 * Gets the session cookie path.
	 * 
	 * @return string The path where the session cookie is available.
	 */
	public static function getPath(): string{
		return session_get_cookie_params()['path'];
	}

	/**
	 * Sets the session cookie domain.
	 * 
	 * @param string $domain The domain. Defaults to an empty string.
	 * 
	 * @throws RuntimeException If the session has already started.
	 */
	public static function setDomain(string $domain = ''): void{
		if(self::isNone())
			session_set_cookie_params(['domain'=>$domain]);
		else
			throw new RuntimeException("Cannot set session cookie domain after the session has already started");
	}

	/**
	 * Gets the session cookie domain.
	 * 
	 * @return string The domain where the session cookie is available.
	 */
	public static function getDomain(): string{
		return session_get_cookie_params()['domain'];
	}

	/**
	 * Sets the session cookie lifetime.
	 * 
	 * @param int $lifetime Lifetime in seconds. Defaults to 0.
	 * 
	 * @throws RuntimeException If the session has already started.
	 */
	public static function setLifetime(int $lifetime = 0): void{
		if(self::isNone())
			session_set_cookie_params(['lifetime'=>$lifetime]);
		else
			throw new RuntimeException("Cannot set session cookie lifetime after the session has already started");
	}

	/**
	 * Gets the session cookie lifetime.
	 * 
	 * @return int Lifetime of the cookie in seconds.
	 */
	public static function getLifetime(): int{
		return session_get_cookie_params()['lifetime'];
	}

	/**
	 * Sets the session cookie secure flag.
	 * 
	 * @param bool $secure True if cookie should be sent over HTTPS only.
	 * 
	 * @throws RuntimeException If the session has already started.
	 */
	public static function setSecure(bool $secure): void{
		if(self::isNone())
			session_set_cookie_params(['secure'=>$secure]);
		else
			throw new RuntimeException("Cannot set session cookie secure flag after the session has already started.");
	}

	/**
	 * Gets the session cookie secure flag.
	 * 
	 * @return bool True if cookie is sent over HTTPS only.
	 */
	public static function getSecure(): bool{
		return session_get_cookie_params()['secure'];
	}

	/**
	 * Sets the session cookie SameSite attribute.
	 * 
	 * @param bool $samesite SameSite attribute value.
	 * 
	 * @throws RuntimeException If the session has already started.
	 */
	public static function setSamesite(bool $samesite): void{
		if(self::isNone())
			session_set_cookie_params(['samesite'=>$samesite]);
		else
			throw new RuntimeException("Cannot set session cookie SameSite attribute after the session has already started");
	}

	/**
	 * Gets the session cookie SameSite attribute.
	 * 
	 * @return bool SameSite attribute value.
	 */
	public static function getSamesite(): bool{
		return session_get_cookie_params()['samesite'];
	}

	/**
	 * Sets the session cookie HttpOnly flag.
	 * 
	 * @param bool $httponly True to make cookie accessible only through HTTP protocol.
	 * 
	 * @throws RuntimeException If the session has already started.
	 */
	public static function setHttponly(bool $httponly): void{
		if(self::isNone())
			session_set_cookie_params(['httponly'=>$httponly]);
		else
			throw new RuntimeException("Cannot set session cookie httponly attribute after the session has already started");
	}

	/**
	 * Gets the session cookie HttpOnly flag.
	 * 
	 * @return bool True if cookie is accessible only through HTTP protocol.
	 */
	public static function getHttponly(): bool{
		return session_get_cookie_params()['httponly'];
	}

	/**
	 * Starts the session if not already active.
	 * 
	 * @return bool True on success, false if session was already active.
	 */
	public static function start(): bool{
		return self::isActive() ? false : session_start();
	}

	/**
	 * Stops (writes and closes) the session.
	 * 
	 * @return bool True on success, false if no session was active.
	 */
	public static function stop(): bool{
		return self::isNone() ? false : session_write_close();
	}

	/**
	 * Aborts the session without saving data.
	 * 
	 * @return bool True on success, false if no session was active.
	 */
	public static function abort(): bool{
		return self::isNone() ? false : session_abort();
	}

	/**
	 * Destroys all session data.
	 * 
	 * @return bool True on success, false if no session was active.
	 */
	public static function destroy(): bool{
		return self::isNone() ? false : session_destroy();
	}

	/**
	 * Empties the session array.
	 * 
	 * @return bool True if session was emptied, false otherwise.
	 */
	public static function empty(): bool{
		if(self::isNone()) return false;
		
		$_SESSION = [];

		return true;
	}

	/**
	 * Retrieves a value from the session.
	 * 
	 * @param string $key The key of the session variable.
	 * 
	 * @return mixed The value of the session variable, or null if not set.
	 */
	public static function get(string $key): mixed{
		return @$_SESSION[$key];
	}

	/**
	 * Sets a session variable or multiple variables.
	 * 
	 * @param array|string $key The key or array of key-value pairs.
	 * @param mixed|null $value The value to set if $key is a string.
	 * 
	 * @return bool True on success, false if session is not active.
	 */
	public static function set(array | string $key, mixed $value = null): bool{
		if(self::isNone()) return false;

		if(is_array($key))
			$_SESSION = [...$_SESSION, ...$key];
		else
			$_SESSION[$key] = $value;

		return true;
	}
}
