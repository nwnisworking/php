<?php
namespace MVC;

if(!defined('BASE_PATH') || !defined('VIEW_PATH')) die('Requires BASE_PATH and VIEW_PATH to be defined');

if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);

final class View{
  private array $script = [];

  private array $link = [];

  private array $meta = [];

  public string $title = '';

  private string $layout;

  private string $page;

  public function layout(string $path): self{
    $this->layout = VIEW_PATH.DS.'layouts'.DS."$path.php";
    return $this;
  }

  public function page(string $page): self{
    $this->layout = VIEW_PATH.DS.'pages'.DS."$page.php";
    return $this;
  }

  public function component(string $component, array $opt = []): string{
    extract($opt);
    ob_start();
    include_once VIEW_PATH.DS.'components'.DS."$component.php";
    return ob_get_clean();
  }

  public function json(array|object $json): string{
    return json_encode($json);
  }

  public function file(string $path, ?int $offset = null, ?int $length): ?string{
    $path = BASE_PATH.DS.'public'.DS.$path;

    if(!file_exists($path)) return null;
    $length = (int)filesize($path);

    return file_get_contents($path, offset: $offset, length: $length);
  }

  public function render(string $page, array $opt = []): string{
    $this->page($page);
    extract($opt);
    ob_start();
    include_once $this->layout;
    return ob_get_clean();
  }

  public function script(string $src, ?string $type = null, ?bool $async = null, ?string $crossorigin = null, ?bool $defer = null, ?string $integrity = null, ?bool $nomodule = null, ?string $referrerpolicy = null): self{
    $this->script[] = $this->templateTag(get_defined_vars(), '<script %s></script>');
    return $this;
  }

  public function link(string $href, ?string $rel = null, ?string $crossorigin = null, ?string $hreflang = null, ?string $media = null, ?string $referrerpolicy = null, ?string $sizes = null, ?string $title = null, ?string $type = null): self{
    $this->link[] = $this->templateTag(get_defined_vars(), '<link %s />');
    return $this;
  }

  public function meta(?string $name = null, ?string $content = null, ?string $charset = null, ?string $http_equiv = null): self{
    $this->meta[] = $this->templateTag(get_defined_vars(), '<meta %s />');
    return $this;
  }

  private function templateTag(array $vars, string $tag): string{
    $arr = array_map(fn($e)=>"'$e'", array_filter($vars, fn($e)=>$e));
    return sprintf($tag, urldecode(http_build_query($arr, '', ' ')));
  }
}