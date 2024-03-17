<?php
namespace HTTP;

final class Response{
	public array $header;

	public function __construct(){
		$this->header = array_change_key_case(apache_response_headers());
	}

	public function set(string $key, string|int $value): self{
		$this->header[$key] = $value;
		header("$key: $value", true);
		return $this;
	}

	public function get(string $key): string|int|null{
		return @$this->header[$key];
	}

	public function status(int $status): self{
		http_response_code($status);
		return $this;
	}

	public function cookie(string $name, mixed $value, int $expires = 0, ?int $max_age = null, string $path = '/', ?string $domain = null, bool $httponly = false, bool $secure = false, ?string $samesite = null): self{
		$cookie = "$name=$value; ";
		if($max_age) $cookie.= "max-age=$max_age; ";
		else $cookie.= 'Expires='.gmdate('D, d M Y H:i:s', $expires).'; ';

		$cookie.= "Path=$path; ";
		
		if($samesite) $cookie.= "SameSite=$samesite; ";
		if($domain) $cookie.= "Domain=$domain; ";
		if($secure) $cookie.= 'Secure; ';
		if($httponly) $cookie.= 'HttpOnly; ';

		return $this->set('set-cookie', rtrim($cookie, '; '));
	}
}