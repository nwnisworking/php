<?php
namespace MVC;

use HTTP\Request;
use HTTP\Response;

abstract class Controller{
  private Request $request;

  private Response $response;

  protected View $view;

  public function __construct(Request $req, Response $res){
    $this->request = $req;
    $this->response = $res;
    $this->view = new View;
  }

  public function json(object|array $json): string{
    return $this->view->json($json);
  }

  public function file(string $path, ?int $offset = null, ?int $length = null): string{
    return $this->view->file($path, $offset, $length);
  }

  public function render(string $page, ?array $opt = []): string{
    return $this->view->render($page, $opt);
  }

  public function layout(string $layout): self{
    $this->view->layout($layout);
    return $this;
  }

  public function status(int $status): self{
    $this->response->status($status);
    return $this;
  }
}