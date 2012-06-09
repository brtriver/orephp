<?php
$now = date('Y-m-d H:i:s');
return $c['tpl']->render('hello.html', array('now' => $now, 'name' => $name));