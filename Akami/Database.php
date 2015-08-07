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
   * NySQL connection data
   *
   * @var array
   */
  protected $config = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => '',
    'pconnect' => true,
    'charset'  => 'utf8'
  );

  /**
   * Database connection
   *
   * @var object
   */
   protected $connection;

   /**
    * Database adapter
    *
    * @var class
    */
   protected $adapter;

   /**
    * SQL Query
    *
    * @var string
    */
   public $query = '';

   /**
    * Error Message
    *
    * @var string
    */
   public $error = '';

   /**
    * Error Number
    *
    * @var int
    */
   public $errno = 0;

   public $num_rows  = 0;
   public $insert_id = 0;
   public $affected_rows = 0;

  /**
   * Class Construction
   */
  public function __construct($config = array())
  {
    if (!$config)
    {
      return false;
    }

    foreach ($config as $key => $value)
    {
      if (in_array($key, $this->config))
      {
        $this->config[$key] = $value;
      }
    }

    $database_type  = isset($config['database_type']) ? strtolower($config['database_type']) : 'mysql';

    switch ($database_type)
    {
      case 'mysql':
        if (isset($config['adapter']))
        {
          $adapter = ucfirst(strtolower($config['addapter']));
        }
          else if (class_exists('MySQLi'))
        {
          $adapter = 'Mysqli';
        }
          else if (class_exists('PDO'))
        {
          $adapter = 'PDO';
        }
          else
        {
          $adapter = 'Mysql';
        }

        break;
    }

    $adapter = '\\Akami\\Database\\' . $adapter;

    $this->adapter = new $adapter;
    $this->adapter->connect();
  }

  /**
   * Class desctruction
   */
  public function __destruct()
  {
    if ($this->adapter)
    {
      $this->adapter->close();
    }
  }
}
