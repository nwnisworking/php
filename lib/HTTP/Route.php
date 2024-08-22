<?php
namespace HTTP;

final class Route{
	private static array $controllers = [];

	private static array $methods = [
		'GET'=>[],
		'POST'=>[],
		'PUT'=>[],
		'HEAD'=>[],
		'DELETE'=>[]
	];

	public readonly string $method;

	public readonly string $path;

	private array $fn = [];

	public function __construct(string $method, ?string $path, mixed $fn){
		$this->method = $method;
		$this->path = $path;
		$this->fn[] = $fn;
	}

	public function middleware(callable|array $fn): self{
		if(is_array($fn))
			self::setController($fn);

		array_unshift($this->fn, $fn);
		return $this;
	}

	public function callback(): array{
		return $this->fn;
	}

	public static function get(?string $path = null, mixed $fn = null): self|array{
		return self::createRoute('GET', $path, $fn);
	}

	public static function post(?string $path = null, mixed $fn = null): self|array{
		return self::createRoute('POST', $path, $fn);
	}

	public static function put(?string $path = null, mixed $fn = null): self|array{
		return self::createRoute('PUT', $path, $fn);
	}

	public static function delete(?string $path = null, mixed $fn = null): self|array{
		return self::createRoute('DELETE', $path, $fn);
	}

	public static function head(?string $path = null, mixed $fn = null): self|array{
		return self::createRoute('HEAD', $path, $fn);
	}

	public static function init(): void{
		foreach(self::$controllers as &$controller)
			if(is_string($controller))
				$controller = new $controller;
	}

	private static function createRoute(string $method, ?string $path, mixed $fn): self|array{
		if(!isset($path, $fn))
			return self::$methods[$method];

		if(is_array($fn))
			self::setController($fn);

		return self::$methods[$method][$path] = new self($method, $path, $fn);
	}

	public static function setController(array &$controller): void{
		$name = $controller[0];

		assert(class_exists($name), "Controller '$name' class is not loaded");

		self::$controllers[$name]??= $name;
		$controller[0] = &self::$controllers[$name];
	}
}