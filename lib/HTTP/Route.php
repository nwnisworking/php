<?php
namespace HTTP;

use Attribute;
use ReflectionClass;
use ReflectionMethod;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Route{
	public readonly string $path;

	public readonly Method $method;

	private array $paths = [];

	private ?array $middlewares;

	public function __construct(string $path, Method $method = Method::GET, ?array ...$middlewares){
		$this->path = $path;
		$this->method = $method;
		$this->middlewares = $middlewares;
	}

	public function add(Route $route, ReflectionMethod $rm): void{
		$path = $this->path.$route->path;

		foreach($this->middlewares as $v)
			$route->addMiddleware($v, true);

		$route->addMiddleware($rm);
		$this->paths[$path][] = $route;
	}

	public function addMiddleware(ReflectionMethod|callable|array $fn, bool $prepend = false): void{
		if($fn instanceof ReflectionMethod)
			$data = [$fn->class, $fn->name];
		else
			$data = $fn;

		if($prepend)
			array_unshift($this->middlewares, $data);
		else
			array_push($this->middlewares, $data);
	}

	public static function collect(string $class): array{
		$rc = new ReflectionClass($class);
		$path = @$rc->getAttributes(self::class);

		if(!count($path))
			return [];

		$path = $path[0]->newInstance();

		foreach($rc->getMethods(ReflectionMethod::IS_PUBLIC) as $rm)
			foreach($rm->getAttributes(self::class) as $r)
				$path->add($r->newInstance(), $rm);

		return $path->paths;
	}
}