<?php
namespace SQL;

use RuntimeException;
use UnexpectedValueException;

class DBConfig{
	private array $config = [];

	private function __construct(array $config){
		$this->config = $config;
	}

	public function get(string $key): mixed{
		return @$this->config[$key];
	}
	
	public static function loadFile(string $path): self{
		if(!is_readable($path))
			throw new RuntimeException("Unable to read file at '$path'");

		$ext = substr($path, strrpos($path,".") + 1);

		switch($ext){
			case 'php' : 
				$temp = include_once $path;

				if(!is_array($temp))
					throw new UnexpectedValueException("Return value expects to be an array");

				return new self($temp);
			case 'json' : 
				$temp = json_decode(file_get_contents("$path"), true);

				return new self($temp);
			default : 
				throw new RuntimeException("Invalid file format. Only .php and .json are accepted");
		}
	}
}