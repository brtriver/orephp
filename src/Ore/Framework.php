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
    $this->c['debug'] = false;
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
    $router = $this->getRouter();
    $request = Request::createFromGlobals();
    try{
      $params = $router->match($request->getPathInfo());
      $controllerFilePath = $this->c['base_dir'] . '/app/controllers/' . $params['controller'] . ".php";
      if (!file_exists($controllerFilePath)) {
        throw new ResourceNotFoundException(sprintf("%s.php is not found", $params['controller']));
      }
      require $controllerFilePath;
      $func = include $controllerFilePath;
    } catch (ResourceNotFoundException $e) {
      $params = (isset($params))? $params: array();
      $func = include $this->c['base_dir'] . '/app/controllers/404.php';
      $this->c['error'] = $e;
    }
    return $func($request, $params, $this->c);
  }
  /**
   * get router
   */
  public function getRouter()
  {
    if (file_exists($this->c['matcher']) || $this->c['debug'] === true) {
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
    return $router;
  }
  /**
   * get response and display
   */
  public function display()
  {
    echo $this->getResponse();
  }
}