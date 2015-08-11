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
  /**
   * Database adapter
   *
   * @var class
   */
  private $adapter;

  /**
   * SQL Query
   *
   * @var string
   */
  protected $query = '';

  /**
   * Log
   */
  protected $logs = array();

  /**
   * Init adapter
   */
  static public function init($config = array())
  {
    if (!$config)
    {
      return false;
    }

    $type = isset($config['database_type']) ? strtolower($config['database_type']) : 'mysql';

    switch ($type)
    {
      case 'mysql':
        if (isset($config['adapter']))
        {
          $adapter = ucfirst(strtolower($config['adapter']));
        }
          else if (class_exists('MySQLi'))
        {
          $adapter = 'MySQLi';
        }
          else if (class_exists('PDO'))
        {
          $adapter = 'PDO';
        }
          else
        {
          $adapter = 'MySQL';
        }

        break;

      case 'mssql':
        break;

      case 'file':
        break;
    }

    $adapter = '\\Akami\\Database\\' . $adapter;
    $adapter = new $adapter($config);

    return $adapter;
  }

  /**
   * Print log
   *
   * @return array
   */
  public function log()
  {
    return $this->logs;
  }
}
