<?php
namespace HTTP;

use Attribute;
use Generator;
use ReflectionClass;
use ReflectionMethod;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Route{
	public readonly string $path;

	public readonly Method $method;

	protected ?array $middlewares;

	public function __construct(string $path, Method $method = Method::GET, ?array ...$middlewares){
		$this->path = $path;
		$this->method = $method;
		$this->middlewares = $middlewares;
	}

	public function getMiddleware(): ?Generator{
		foreach($this->middlewares as $middleware)
			yield $middleware;

		return null;
	}

	public static function collect(string $class): array{
		$cls = new ReflectionClass($class);
		$methods = [];
		$main_route = @$cls->getAttributes(self::class);

		if(!count($main_route))
			return [];

		// For main route, only the first attribute is selected
		$main_route = $main_route[0]->newInstance();

		foreach($cls->getMethods(ReflectionMethod::IS_PUBLIC) as $m){
			foreach($m->getAttributes(self::class) as $r){
				$route = $r->newInstance();
				$path = $main_route->path.$route->path;
				$methods[$route->method->value][$path] = $route;
				$route->middlewares[] = [$m->class, $m->name];
				
				array_splice($route->middlewares, 0, 0, $main_route->middlewares);
			}
		}

		return $methods;
	}
}