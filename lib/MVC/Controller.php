<?php
namespace MVC;

use HTTP\Request;
use HTTP\Response;

abstract class Controller{
	public function __construct(
		private Request $request,
		private Response $response
	){}

	public function header(string $key, mixed $value = null): self{
		$this->response->set($key, $value);
		return $this;
	}

	public function removeHeader(string $key): self{
		$this->response->remove($key);
		return $this;
	}

	public function json(object|array $json): string{
		return json_encode($json);
	}

	public function render(string $path): string{
		assert(file_exists($path), 'File cannot be rendered');

		ob_start();
		include_once $path;
		return ob_get_clean();
	}

  public function file(string $path, ?int $offset = null, ?int $length = null): string{
		assert(file_exists($path), 'File does not exists');
		
		return file_get_contents($path, offset: $offset ?? 0, length: $length ?? filesize($path));
  }

  public function status(int $status): self{
		$this->response->status($status);
    return $this;
  }

	public function model(string $model): Model{
		assert(class_exists($model, true), "Model $model does not exists");

		return new $model;
	}
}