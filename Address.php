<?php
/**
 * Final class Address
 * Represent an IP address and port combination.
 */
final readonly class Address{
	/**
	 * @var string The IP address.
	 */
	public string $ip;

	/**
	 * @var int|null The port number. Null if not specified.
	 */
	public ?int $port;

	/**
	 * Constructor for the Address class.
	 *
	 * @param string $ip The IP address. Can include an optional port in the format "[IP]:port", "IP:port" or "IP".
	 * @param int|null $port The port number (optional). Overrides any port specified in the $ip string.
	 */
	public function __construct(string $ip, ?int $port = null){
		if (is_null($port) && is_int(preg_match('/^\[?(?<ip>[\w.]+|[\w:]+)\]?(?::(?<port>\d+))?$/', $ip, $matches))){
			$port = $matches['port'] ?? null;
			$ip = $matches['ip'];
		}

		$this->ip = $ip;
		$this->port = $port;
	}

	/**
	 * Converts the IP address to its binary representation.
	 *
	 * @return string|bool Returns the binary representation of the IP address or false on failure.
	 */
	public function ip(): string|bool{
		return inet_pton($this->ip);
	}

	/**
	 * Converts the port number to its binary representation.
	 *
	 * @return string|bool Returns the binary representation of the port or false if no port is set.
	 */
	public function port(): string|bool{
		return $this->port ? pack('n', $this->port) : false;
	}

	/**
	 * Determines if the IP address is IPv4.
	 *
	 * @return bool True if the IP address is IPv4, false otherwise.
	 */
	public function isIPV4(): bool{
		return filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	}

	/**
	 * Determines if the IP address is IPv6.
	 *
	 * @return bool True if the IP address is IPv6, false otherwise.
	 */
	public function isIPV6(): bool{
		return filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	}

	public function validIP(): bool{
		return $this->isIPV4() || $this->isIPV6();
	}

	/**
	 * Converts the Address object to a string representation.
	 *
	 * @return string The string representation of the address in the format "IP" or "[IP]:port".
	 */
	public function __toString(){
		$ip = $this->ip;
		$port = $this->port;

		return ($this->isIPV6() ? "[$ip]" : $ip) . (is_null($port) ? '' : ":$port");
	}
}
