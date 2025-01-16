<?php
namespace Web;

assert(function_exists('\apache_response_header'), "class Web\\Response requires Web Server to run");
assert(class_exists("Address", false), "Address is required by the Request class");

use Address;

/**
 * Final class for handling HTTP responses, including headers, status, and cookies.
 */
final class Response {
    /**
     * @var array The HTTP headers for the response.
     */
    public readonly array $header;

    /**
     * Constructor for the Response class. Initializes the response headers.
     * 
     * @throws AssertionError If the Apache server or Address class is not available.
     */
    public function __construct() {
        $this->header = array_change_key_case(apache_response_headers());
    }

    /**
     * Sets a response header or multiple headers.
     *
     * @param string|array $key The header name, or an associative array of headers.
     * @param mixed $value The header value if a single header is provided.
     * @return static Returns the current instance to allow method chaining.
     */
    public function set(string|array $key, mixed $value = null): static {
        if (is_array($this->header)) {
            foreach ($key as $k => $v) {
                header("$k: $v", true);
            }

            $this->header = array_merge($this->header, $key);
        } else {
            header("$key: $value", true);
            $this->header[$key] = $value;
        }

        return $this;
    }

    /**
     * Removes a header from the response.
     *
     * @param string $key The header to remove.
     * @return static Returns the current instance to allow method chaining.
     */
    public function remove(string $key): static {
        unset($this->header[$key]);
        header_remove($key);

        return $this;
    }

    /**
     * Retrieves the value of a specific header.
     *
     * @param string $key The header name to retrieve.
     * @return mixed The header value, or null if not set.
     */
    public function get(string $key): mixed {
        return @$this->header[$key];
    }

    /**
     * Sets the HTTP response status code.
     *
     * @param int $status The HTTP status code to set.
     * @return static Returns the current instance to allow method chaining.
     */
    public function status(int $status): static {
        http_response_code($status);
        
        return $this;
    }

    /**
     * Sets a cookie in the response.
     *
     * @param string $name The name of the cookie.
     * @param mixed $value The value of the cookie.
     * @param int $expires The expiration time of the cookie.
     * @param string $path The path where the cookie is available.
     * @param string $domain The domain for the cookie.
     * @param bool $secure Whether the cookie should only be sent over secure connections.
     * @param bool $httponly Whether the cookie should be accessible only via HTTP(S), not JavaScript.
     * @return static Returns the current instance to allow method chaining.
     */
    public function cookie(
        string $name, 
        mixed $value, 
        int $expires = 0,
        string $path = '',
        string $domain = '',
        bool $secure = false,
        bool $httponly = false
    ): static {
        setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);

        return $this;
    }
}
