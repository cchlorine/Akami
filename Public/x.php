<?php

require('../Akami/Akami.php');
\Akami\Akami::registerAutoloader();

$app = new \Akami\Akami;
$app->get('/', function() {
  echo 'Hello, World!';
});

$app->get('/hello/(\w+)', function($name) {
  echo 'Hello, ' . $name;
});

$app->get('/test/(\d+)', function($id) {
  echo 'TestID: ' . $id;
});

$app->run();