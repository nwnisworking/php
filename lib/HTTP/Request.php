<?php
namespace HTTP;

use Address;
use Method;

final class Request{
	public readonly array $header;

	public function __construct(){
		$this->header = array_change_key_case(apache_request_headers());
	}

	public function get(string $key): string|array|null{
		if(!isset($_GET[$key])) return null;
		return is_array($_GET[$key]) ? array_map([self::class, 'htmlchar'], $_GET[$key]) : self::htmlchar($_GET[$key]);
	}

	public function post(string $key): string|array|null{
		if(!isset($_POST[$key])) return null;
		return is_array($_POST[$key]) ? array_map([self::class, 'htmlchar'], $_POST[$key]) : self::htmlchar($_POST[$key]);
	}

	public function userAgent(): string{
		return $this->header['user-agent'];
	}

	public function method(): Method{
		return Method::from($_SERVER['REQUEST_METHOD']);
	}

	public function uri(): string{
		return str_replace($_ENV['PATH'], '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	}

	public function clientIp(): Address{
		return new Address($_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT']);
	}

	public function inPost(string ...$post): bool{
		foreach(array_diff($post, array_keys($_POST)) as $v){
			return !in_array($v, $post);
		}

		return true;
	}

	public function inGet(string ...$get): bool{
		foreach(array_diff($get, array_keys($_GET)) as $v){
			return !in_array($v, $get);
		}

		return true;
	}

	public function input(): array|string{
		$str = file_get_contents('php://input');

		if(!($res = json_decode($str, true)))
			return $str;
		else
			return $res;
	}

	private static function htmlchar(string $e): string{
		return htmlspecialchars($e, ENT_COMPAT | ENT_HTML5 | ENT_SUBSTITUTE);
	}
}