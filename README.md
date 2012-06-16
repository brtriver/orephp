OrePHP, simple web application framework for PHP
==================================================

OrePHP is a web application routing framework for PHP with Symfony Component
You have only to define routing.yml, OrePHP call the controller php file.
This controller php file is not a class file but a plain PHP file.
In this controller file, you have to return strings as response data.

Simple and Fast
---------------
Core code is only 83 lines so much fast.
It has DI container(Pimple) and Twig template system.
It is fit for you developing simple applications.

Routing
-------
OrePHP has a routing system and this is just same as Symfony one.

`routing.yaml`

    hello:
        pattern: /hello/{name}
        defaults: { controller: 'hello' }
    default:
        pattern: /{controller}
        defaults: { controller: controller }

Once this file is parsed, this routing is cached in the cache directory as ProjectUrlMatcher.php.
If you want to change the routing after this routing is cached, you have to remove this file by hand.


Controller file
---------------
Controller file is php file having closure function and you have to return Response strings with return like below:

    <?php
    return function($request, $params, $container) {
        $now = date('Y-m-d H:i:s');
        return "Hello world " . $params['name'];
    };


If you want to use Twig as a template engine, you call `$container['tpl']->render` method like below:

    <?php
    return function($request, $params, $container) {
        $now = date('Y-m-d H:i:s');
        return $container['tpl']->render('hello.html', array('now' => $now, 'name' => $params['name']));
    };

if you don't use Twig, Twig object is not created so it is very eco system.

Quick Installation
------------------

- install by composer

    $ php composer.phar install
    $ chmod 0777 cache

- access to the front controller

    http://example.com/hello/orephp

Licence
-------
OrePHP is released under the MIT license.
