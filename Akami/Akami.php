<?php
/**
 * Akami Core
 *
 * @author Rakume Hayashi <i@fake.moe>
 * @copyright 2015 Lingoys!Art.
 * @version 1.0
 * @package Akami
 */

namespace Akami;

class Akami
{
  /**
   * @const string
   */
  const VERSION = '1.0';

  /**
   * Errors log
   *
   * @var array
   */
  protected $error = array();

  /**
   * a simple PSR-0 autoloader
   *
   * @param string $className className of the file
   */
  static public function autoload($className)
  {
    $className = ltrim($className, '\\');

    if ($lastNamespacePos = strripos($className, '\\')) {
      $namespace = substr($className, 0, $lastNamespacePos);
      $className = substr($className, $lastNamespacePos + 1);
      $fileName  = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    if (file_exists($fileName)) {
      require $fileName;
    }
  }

  /**
   * Register Slim's PSR-0 autoloader
   * 
   * @return void
   */
  static public function registerAutoloader()
  {
    spl_autoload_register(__NAMESPACE__ . '\\Akami::autoload');
  }

  /**
   * Constructor Akami
   * 
   * @return \Akami\Akami
   */
  public function __construct()
  {
    $this->router = new \Akami\Router;

    return $this;
  }

  /**
   * Add route
   * 
   * @param string $method
   * @param string $route
   * @param mixed  $callback
   * @return \Akami\Akami
   */
  public function add($method, $route, $callback)
  {
    $this->router->add($method, $route, $callback);

    return $this;
  }

  /**
   * Add get route
   * 
   * @param string $route
   * @param mixed  $callback
   * @return \Akami\Akami
   */
  public function get($route, $callback)
  {
    return $this->add('GET', $route, $callback);
  }

  /**
   * Add post route
   * 
   * @param  string $route
   * @param  mixed  $callback
   * @return \Akami\Akami
   */
  public function post($route, $callback)
  {
    return $this->add('POST', $route, $callback);
  }

  /**
   * Add put route
   * 
   * @param  string $route
   * @param  mixed  $callback
   * @return \Akami\Akami
   */
  public function put($route, $callback)
  {
    return $this->add('PUT', $route, $callback);
  }

  /**
   * Add delete route
   * 
   * @param  string $route
   * @param  mixed  $callback
   * @return \Akami\Akami
   */
  public function delete($route, $callback)
  {
    return $this->add('DELETE', $route, $callback);
  }

  /**
   * Run the app
   * 
   * @return void
   */
  public function run()
  {
    set_error_handler(array('\Akami\Akami', 'handleErrors'));
    $this->router->route();
    restore_error_handler();
  }

  /**
   * Convert errors into ErrorException objects
   *
   * @param int    $errno The numeric type of the error
   * @param string $str   The error Message
   * @param string $file  The path to the affected file
   * @param string $line  The line of the affected file
   * @throws \ErrorException
   */
  static public function handleErrors($errno, $str = '', $file = '', $line = '')
  {
    if (!($errno && error_reporting())) {
      return;
    }

    throw new \ErrorException($str, $errno, 0, $file, $line);
  }

  /**
   * Get application instance
   *
   * @return \Akami\Akaimi
   */
  static public function getInstance()
  {
    return $this;
  }
}
