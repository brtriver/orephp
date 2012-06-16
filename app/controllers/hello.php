<?php
return function($request, $params, $container) {
    $now = date('Y-m-d H:i:s');
    //return "Hello world " . $params['name'];
    return $container['tpl']->render('hello.html', array('now' => $now, 'name' => $params['name']));
};

