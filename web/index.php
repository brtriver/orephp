<?php
ini_set('display_errors', 1);
error_reporting(-1);

require __DIR__ . '/../vendor/autoload.php';

$app = new Ore\Framework;
$app->c['tpl'] = $app->c->share(function($c){
    $loader = new Twig_Loader_Filesystem($c['base_dir'] . '/app/views');
    return new Twig_Environment($loader,array(
                                  'cache' => $c['cache_dir'],
                                  ));
  });
// $app->c['debug'] = true;
$app->display();