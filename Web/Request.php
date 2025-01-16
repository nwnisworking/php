<?php
namespace Web;

assert(function_exists('\apache_request_header'), "class Web\\Request requires Web Server to run");
assert(class_exists("Address", false), "Address is required by the Request class");

use Address;

/**
 * Final class that handles HTTP request data.
 * 
 * This class provides methods to access and process HTTP request data such as headers, GET and POST parameters,
 * client and server IPs, request method, and URI. It also sanitizes input for security.
 */

final class Request {
	/**
	 * @var array The request headers, with keys normalized to lowercase.
	 */
	public readonly array $header;

	/**
	 * Constructor for the Request class.
	 * Initializes the $header property by retrieving all headers from the Apache server.
	 */
	public function __construct() {
		$this->header = array_change_key_case(apache_request_headers());
	}

	/**
	 * Retrieves a value from the GET parameters.
	 *
	 * @param string $key The key of the GET parameter.
	 * @return string|array|null The value(s) of the GET parameter, or null if not set.
	 */
	public function get(string $key): string|array|null {
		$value = $_GET[$key];

		if (!isset($value))
			return null;

		if (is_array($value)) {
			return array_map([self::class, 'specialChars'], $value);
		}
		else {
			return self::specialChars($value);
		}
	}

	/**
	 * Retrieves a value from the POST parameters.
	 *
	 * @param string $key The key of the POST parameter.
	 * @return string|array|null The value(s) of the POST parameter, or null if not set.
	 */
	public function post(string $key): string|array|null {
		$value = $_POST[$key];
			
		if (!isset($value))
			return null;

		if (is_array($value)) {
			return array_map([self::class, 'specialChars'], $value);
		}
		else {
			return self::specialChars($value);
		}
	}

	/**
	 * Retrieves the HTTP request method (GET, POST, etc.).
	 *
	 * @return string The request method.
	 */
	public function method(): string {
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Retrieves the URI path of the request (excluding query string).
	 *
	 * @return string The URI path.
	 */
	public function uri(): string {
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}

	/**
	 * Retrieves the client's IP address as an Address object.
	 *
	 * @return Address The client's IP address.
	 */
	public function clientIP(): Address {
		return new Address($_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT']);
	}

	/**
	 * Retrieves the server's IP address as an Address object.
	 *
	 * @return Address The server's IP address.
	 */
	public function serverIP(): Address {
		return new Address($_SERVER['SERVER_ADDR'], $_SERVER['SERVER_PORT']);
	}

	/**
	 * Retrieves the raw input data from the request body.
	 *
	 * @return string The raw POST data.
	 */
	public function input(): string {
		return file_get_contents('php://input');
	}

	/**
	 * Sanitizes input by converting special characters to HTML entities.
	 *
	 * @param string $text The text to sanitize.
	 * @return string The sanitized text.
	 */
	private static function specialChars(string $text): string {
		return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8', false);
	}
}
