<?php
/**
 * Database Connection
 *
 * @author Rakume Hayashi <i@fake.moe>
 * @copyright 2015 Lingoys!Art.
 * @version 1.0
 * @package Akami
 */

namespace Akami\Database;

use PDO;

class Connection
{
  /**
   * Database connection
   */
  protected $connection;

  /**
   * Excute logs
   *
   * @var array
   */
  protected $logs = [];

  /**
   * The where constraints for the query
   *
   * @var array
   */
  protected $wheres = [];

  /**
   * All of the available clause operators.
   *
   * @var array
   */
  protected $operators = [
    '=', '<', '>', '<=', '>=', '<>', '!=',
    'like', 'like binary', 'not like', 'between', 'ilike',
    '&', '|', '^', '<<', '>>',
    'rlike', 'regexp', 'not regexp',
    '~', '~*', '!~', '!~*', 'similar to',
    'not similar to',
  ];

  /**
   * Columns
   *
   * @var array
   */
  public $columns = [];

  /**
   * Construct connection
   *
   * @param array $config
   */
  public function __construct($config = [])
  {
    if (!$config)
    {
      return false;
    }

    $adapter = isset($config['adapter']) ? strtolower($config['adapter']) : 'mysql';
    $querys = [];

    switch ($adapter) {
      case 'mariadb':
        $adapter = 'mysql';

      case 'mysql':
        if (isset($config['socket'])) {
          $dsn = 'mysql:unix_socket=' . $config['socket']
               . ';db_name' . $config['database'];
        }
          else
        {
          $dsn = 'mysql:host=' . $config['hostname']
               . (isset($config['port']) ? ';port=' . $config['port'] : '')
               . ';dbname=' . $config['database'];
        }

        $querys[] = 'SET SQL_MODE=ANSI_QUOTES';
        break;
    }

    if (in_array($adapter, explode(' ', 'mariadb mysql pgsql sybase mssql')) &&
      isset($config['charset']))
    {
      $querys[] = "SET NAMES '" . $config['charset'] . "'";
    }

    $this->connection = new PDO($dsn, $config['username'], $config['password']);

    foreach ($querys as $query) {
      $this->exec($query);
    }
  }

  /**
   * Execute a query
   *
   * @param string $query
   * @return int
   */
  public function exec($query)
  {
    array_push($this->logs, $query);
    return $this->connection->exec($query);
  }

  /**
   * Run query
   *
   * @param string $query
   * @return \PDOStatement
   */
  public function query($query)
  {
    array_push($this->logs, $query);
    return $this->connection->query($query);
  }

  /**
   * Set the columns
   *
   * @param array $columns
   * @return \Akami\Database\Connection
   */
  public function select($columns = ['*'])
  {
    $this->columns = is_array($columns) ? $columns : func_get_args();
    return $this;
  }

  /**
   * Add a where clause to query
   *
   * @param string|array $column
   * @param string $operator
   * @param mixed  $value
   * @param string $boolean
   * @return \Akami\Database\Connection
   */
  public function where($column, $operator = null, $value = null, $boolean = 'and')
  {
    if (is_array($column))
    {
      foreach ($column as $key => $value) {
        $this->where($key, '=', $value);
      }

      return $this;
    }

    if (func_num_args() === 2)
    {
      list($value, $operator) = [$operator, '='];
    }

    if (!in_array(strtolower($operator), $this->operators, true))
    {
      list($value, $operator) = [$operator, '='];
    }

    array_push($this->wheres, compact('column', 'operator', 'value', 'boolean'));

    return $this;
  }

  /**
   * Set the offset
   *
   * @param int $value
   * @return \Akami\Database\Connection
   */
  public function offset($value)
  {
    $this->offset = max(0, $value);

    return $this;
  }

  /**
   * Set the limit
   *
   * @param int $value
   * @return \Akami\Databse\Connection
   */
  public function limit($value)
  {
    if ($value > 0)
    {
      $this->limit = $value;
    }

    return $this;
  }

  /**
   * Execute the query
   *
   * @param array $columns
   * @return array
   */
  public function get($columns = ['*'])
  {
    if (is_null($this->columns))
    {
      $this->columns = $columns;
    }
  }

  /**
   * Execute the query and get the frist result
   *
   * @param array $columns
   */
  public function first($columns = ['*'])
  {
    $results = $this->limit(1)->get($columns);

    return count($results) > 0 ? reset($results) : null;
  }

  /**
   * Show excute logs
   *
   * @return array
   */
  public function logs()
  {
    return $this->logs;
  }
}
