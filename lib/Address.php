<?php
final class Address{
	public readonly string $ip;
	
	public readonly ?int $port;

	public readonly int $type;

	public function __construct(string $ip, ?int $port = null){
		if(!self::isIP($ip))
			throw new Exception("Invalid IP Address");

		$this->ip = $ip;
		$this->port = $port;
		$this->type = self::isIPV4($ip) ? AF_INET : AF_INET6;
	}

	public static function isIPV4(string $ip): bool{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	}

	public static function isIPV6(string $ip): bool{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	}

	public static function isIP(string $ip): bool{
		return self::isIPV4($ip) || self::isIPV6($ip);
	}

	public function __toString(){
		return $this->ip.(isset($this->port) ? ":$this->port" : '');
	}
}