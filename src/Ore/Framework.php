<?php
namespace Ore;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Framework 
{
  public $c;
  public function __construct()
  {
    $this->setDefaultParameters();
  }
  /**
   * set default parameters to DI container
   */
  public function setDefaultParameters()
  {
    $this->c = new \Pimple();
    $this->c['context'] = function() {
      return new RequestContext($_SERVER['REQUEST_URI']);
    };
    $this->c['base_dir'] = __DIR__ . '/../..';
    $this->c['cache_dir'] = $this->c['base_dir'] . '/cache';
    $this->c['config_dir'] = $this->c['base_dir'] . '/config';
    $this->c['matcher'] = $this->c['cache_dir'] . '/ProjectUrlMatcher.php';
  }
  /**
   * get response string
   */
  public function getResponse()
  {
    if (file_exists($this->c['matcher'])) {
      require $this->c['matcher'];
      $router = new \ProjectUrlMatcher($this->c['context']);
    } else {
      $locator = new FileLocator(array($this->c['config_dir']));
      $router = new Router(
        new YamlFileLoader($locator),
        "routing.yaml",
        array('cache_dir' => $this->c['cache_dir']),
        $this->c['context']
        );
    }
    $request = Request::createFromGlobals();

    try{
      extract($router->match($request->getPathInfo()), EXTR_SKIP);
      $c = $this->c;
      $controllerFilePath = $this->c['base_dir'] . '/app/controllers/' .$controller . ".php";
      if (file_exists($this->c['matcher'])) {
        throw new ResourceNotFoundException(sprintf("%s.php is not found", $controller));
      }
      $response = include $controllerFilePath;
      return $response;
    } catch (ResourceNotFoundException $e) {
      $response = include $this->c['base_dir'] . '/app/controllers/404.php';
      return $response;
    }
  }
  /**
   * get response and display
   */
  public function display()
  {
    echo $this->getResponse();
  }
}