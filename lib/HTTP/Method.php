<?php
namespace HTTP;

enum Method: string{
	case GET = 'GET';

	case POST = 'POST';

	case PUT = 'PUT';
	
	case DELETE = 'DELETE';
	
	case PATCH = 'PATCH';
	
	case HEAD = 'HEAD';

	case OPTIONS = 'OPTIONS';
}