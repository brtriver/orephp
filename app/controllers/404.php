<?php
return function($request, $params, $container) {
  $error = $container['error']->getMessage();
  if ($container['debug']) {
    $error .= "<pre>" . print_r($container['error'], true) . "</pre>";
  }
  return $error;
};
