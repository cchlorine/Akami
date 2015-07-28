<?php

require('../Akami/Akami.php');
\Akami\Akami::registerAutoloader();

$app = new \Akami\Akami;
$app->get('/', function() {
  echo 'Hello, World!';
});