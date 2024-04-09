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
}