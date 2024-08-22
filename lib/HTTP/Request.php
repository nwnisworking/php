<?php
namespace HTTP;

final class Request{
	public readonly array $header;

	public function __construct(){
		$this->header = array_change_key_case(apache_request_headers());
	}

	public function get(string $key): string|array|null{
		if(!isset($_GET[$key]))
			return null;

		else if(is_array($_GET[$key]))
			return array_map([self::class, 'htmlspecialchars'], $_GET[$key]);

		return self::htmlspecialchars($_GET[$key]);
	}

	public function post(string $key): string|array|null{
		if(!isset($_POST[$key]))
			return null;

		else if(is_array($_POST[$key]))
			return array_map([self::class, 'htmlspecialchars'], $_POST[$key]);

		return self::htmlspecialchars($_POST[$key]);
	}

	public function form(string $form): Form{
		return new $form($this);
	}

	public function method(): string{
		return $_SERVER['REQUEST_METHOD'];
	}

	public function userAgent(): string{
		return $this->header['user-agent'];
	}

	public function uri(): string{
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}

	public function clientIp(): object{
		return (object)['address'=>$_SERVER['REMOTE_ADDR'], 'port'=>$_SERVER['REMOTE_PORT']];
	}

	public function serverIp(): object{
		return (object)['address'=>$_SERVER['SERVER_ADDR'], 'port'=>$_SERVER['SERVER_PORT']];
	}

	public function input(): array|string{
		return file_get_contents('php://input');
	}

	private static function htmlspecialchars(string $e): string{
		return htmlspecialchars($e, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE);
	}
}