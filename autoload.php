<?php
/*
 * Copyright 2015 Dooplenty.
 */

function dooplenty_api_php_client_autoload($className) {
  $classPath = explode('_', $className);

  if ($classPath[0] != 'Fantasy') {
    return;
  }
  if (count($classPath) > 3) {
    // Maximum class file path depth in this project is 3.
    $classPath = array_slice($classPath, 0, 3);
  }

  $filePath = dirname(__FILE__) . '/src/' . implode('/', $classPath) . '.php';
  if (file_exists($filePath)) {
    require_once($filePath);
  }
}

spl_autoload_register('dooplenty_api_php_client_autoload');

require_once('vendor/autoload.php');
