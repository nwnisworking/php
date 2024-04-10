<?php
namespace HTTP;

enum Method{
	case GET;

	case POST;

	case PUT;
	
	case DELETE;
	
	case PATCH;
	
	case HEAD;

	case OPTIONS;

	public static function tryFrom(string $method): ?Method{
		$case = self::cases();
		$arr = array_combine(array_map(fn($e)=>$e->name, $case), $case);

		return @$arr[$method];
	}
}