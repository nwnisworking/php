<?php
/**
 * Represents an IP address with an optional port.
 *
 * @readonly
 */
final readonly class Address {
	/** 
	 * The IP address.
	 * 
	 * @var string
	 */
	public string $ip;

	/** 
	 * The port number, or null if unspecified.
	 * 
	 * @var int|null
	 */
	public ?int $port;

	/**
	 * Constructs an Address instance.
	 *
	 * Parses IP and port from a single string if port is not explicitly provided.
	 *
	 * @param string $ip The IP address or IP:port combination.
	 * @param int|null $port The port number, or null.
	 */
	public function __construct(string $ip, ?int $port = null) {
		if (is_null($port) && is_int(preg_match('/^\[?(?<ip>[\w.]+|[\w:]+)\]?(?::(?<port>\d+))?$/', $ip, $matches))) {
			$port = $matches['port'] ?? null;
			$ip = $matches['ip'];
		}

		$this->ip = $ip;
		$this->port = $port;
	}

	/**
	 * Converts the IP address to its binary representation.
	 *
	 * @return string|bool Binary IP address on success, false on failure.
	 */
	public function ip(): string|bool {
		return inet_pton($this->ip);
	}

	/**
	 * Packs the port number into a network-order binary string.
	 *
	 * @return string|bool Packed port on success, false if no port is set.
	 */
	public function port(): string|bool {
		return $this->port ? pack('n', $this->port) : false;
	}

	/**
	 * Determines if the IP address is a valid IPv4 address.
	 *
	 * @return bool True if IPv4, false otherwise.
	 */
	public function isIPV4(): bool {
		return filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	}

	/**
	 * Determines if the IP address is a valid IPv6 address.
	 *
	 * @return bool True if IPv6, false otherwise.
	 */
	public function isIPV6(): bool {
		return filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	}

	/**
	 * Determines if the IP address is valid (either IPv4 or IPv6).
	 *
	 * @return bool True if valid IP, false otherwise.
	 */
	public function validIP(): bool {
		return $this->isIPV4() || $this->isIPV6();
	}

	/**
	 * Returns the string representation of the address.
	 *
	 * IPv6 addresses are enclosed in square brackets.
	 *
	 * @return string The formatted address.
	 */
	public function __toString() {
		$ip = $this->ip;
		$port = $this->port;

		return ($this->isIPV6() ? "[$ip]" : $ip) . (is_null($port) ? '' : ":$port");
	}
}
