<?php
/**
 * Database
 *
 * @author Rakume Hayashi <i@fake.moe>
 * @copyright 2015 Lingoys!Art.
 * @version 1.0
 * @package Akami
 */

namespace Akami;

class Database
{
  static protected $connection;

  /**
   * Init database
   *
   * @param array $config
   */
  public function init($config = [])
  {
    self::$connection = new \Akami\Database\Connection($config);
  }

  /**
   * Call static
   *
   * @param string $method
   * @param array  $parameters
   */
  static public function __callStatic($method, $parameters)
  {
    return call_user_func_array([
      self::$connection,
      $method
    ], $parameters);
  }
}
