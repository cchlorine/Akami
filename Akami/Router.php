<?php
/**
 * Akami Router
 *
 * @author Rakume Hayashi <i@fake.moe>
 * @copyright 2015 Lingoys!Art.
 * @version 1.0
 * @package Akami
 */

namespace Akami;

class Router
{
  /**
   * Method
   *
   * @var string
   */
  protected $method;

  /**
   * Route
   *
   * @var array
   */
  protected $route = array();

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct()
  {
    // Get the method
    $this->method = $_SERVER['REQUEST_METHOD'];

    // Get the pathinfo
    $this->pathinfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
  }

  /**
   * Add Route
   *
   * @param mixed    $method
   * @param string   $route
   * @param callable $callback
   * @return \Akami\Router
   */
  public function add($methods, $pattern, $callback)
  {
    // Trim the `/` from $pattern
    $route = trim($pattern, '/');

    // Push the array to the route
    foreach (explode('|', $methods) as $method) {

      // When $callback is callable
      if (is_callable($callback)) {
        $this->route[$method][] = compact('pattern', 'callback');
      }
    }

    return $this;
  }

  /**
   * Match the route
   *
   * @param string   $url
   * @param callable $callback
   * @return void
   */
  public function route($url = '', $callback = null)
  {
    // When it cannot found the method in the route
    if (empty($this->route[$this->method])) {
      return;
    }

    // When it has custom RouteURL
    $url = $url === '' ? $this->pathinfo : $url;

    array_walk($this->route[$this->method], function($route) use ($url) {
      // When match the pattern
      if (preg_match_all('#^' . $route['pattern'] . '$#', $url, $matches, PREG_OFFSET_CAPTURE)) {
        $matches = array_slice($matches, 1);

        // Get the param(s)
        $params = array_map(function ($match, $index) use ($matches) {
          if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
            return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
          } else {
            return (isset($match[0][0]) ? trim($match[0][0], '/') : null);
          }
        }, $matches, array_keys($matches));

        // Run the callback
        call_user_func_array($route['callback'], $params);
      } else { // When cannot match the route
        if (is_callable($callback)) {
          call_user_func($callback);
        }
      }
    });
  }
}
