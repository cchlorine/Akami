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
    // Trim `\` from $className
    $className = ltrim($className, '\\');

    // The base dir
    $fileName = dirname(__DIR__) . DIRECTORY_SEPARATOR;

    // When it has namespace
    if ($lastNamespacePos = strripos($className, '\\')) {
      $namespace = substr($className, 0, $lastNamespacePos);
      $className = substr($className, $lastNamespacePos + 1);
      $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    // When exists the file
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
    // New a router
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
   * Configure Akami Settings
   *
   * @param  string|array $name
   * @param  mixed        $value
   * @return mixed
   */
  public function config($name, $value = null)
  {
    // When $name is array
    if (is_array($name)) {
      // When $value is true
      if (true === $value) {
        $this->settings = array_merge_recursive($this->settings, $name);
      } else {
        $this->settings = array_merge($this->settings, $name);
      }
    // When $name is string
    } else if (func_num_args() === 1 && is_string($name)) {
      return isset($this->settings[$name]) ? $this->settings[$name] : null;
    } else {
      $this->settings[$name] = $value;
    }

    return $this;
  }

  /**
   * Run the app
   * 
   * @return void
   */
  public function run($routeUrl = '')
  {
    set_error_handler(array('\Akami\Akami', 'handleErrors'));

    // Route the url
    $this->router->route($routeUrl);

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
    // When it cannot catch the error
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
