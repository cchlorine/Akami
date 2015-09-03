<?php
/**
 * Database / Mysqli
 *
 * @author Rakume Hayashi <i@fake.moe>
 * @copyright 2015 Lingoys!Art.
 * @version 1.0
 * @package Akami
 */

namespace Akami\Database;

class MySQLi extends \Akami\Database
{
  /**
   * MySQL connection data
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
   * Class construction
   *
   * @return void
   */
  public function __construct($config)
  {
    // Load Config
    foreach ($config as $key => $value)
    {
      if (in_array($key, $this->config))
      {
        $this->config[$key] = $value;
      }
    }

    if (!$this->connection) {
      $this->connect();
    }
  }

  /**
   * Connect to the mysqli
   *
   * @return \MySQLi
   */
  protected function connect()
  {
    $config   = $this->config;
    $hostname = $config['pconnect'] === true ? 'p:' . $config['hostname'] : $config['hostname'];

    $this->connection = new \MySQLi($config['hostname'], $config['username'], $config['password'], $config['database']);
    $this->connection->set_charset($config['charset']);

    return $this->connection;
  }

  /**
   * Select Database
   *
   * @param string $database
   * @return boolean
   */
  public function selectDb($database = '')
  {
    return $this->connection->select_db($database);
  }

  /**
   * Check the availability of the database connection
   *
   * @return \MySQLi
   */
  public function check()
  {
    if (empty($this->connection) || !$this->connection->ping())
    {
      $this->connect();
    }

    return $this->connection;
  }

  /**
   * Excute query
   *
   * @param string $query
   * @return array
   */
  protected function exec($query)
  {
    $this->check();
    array_push($this->logs, $query);

    return $this->connection->query($query);
  }

  /**
   * Query
   *
   * @param string $query
   * @return array
   */
  public function query($query)
  {
    $data = array();
    $result = $this->exec($query);

    if ($this->affectedRows() > 0)
    {
      $data = $result->fetch_all(MYSQLI_ASSOC);
      $result->free();
    }
      else if (preg_match('/^select/i', trim($query)))
    {
      return null;
    }
      else
    {
      return true;
    }

    return $data;
  }

  /**
   * Produce where clause
   *
   * @param array $where
   * @return string
   */
  protected function where($where)
  {
    $conditions = array();

    foreach ($where as $key => $value)
    {
      $conditions[] = '`' . $key . '` = "' . $this->escapeValue($value) . '"';
    }

    return ' WHERE ' . implode(' AND ', $conditions);
  }

  /**
   * Produce set clause
   *
   * @param array $where
   * @return string
   */
  protected function set($conditions)
  {
    $conditions = array();

    foreach ($where as $key => $item)
    {
      $conditions[] = '`' . $key . '` = "' . $this->escapeValue($item) . '"';
    }

    return ' SET ' . implode(', ', $conditions);
  }

  /**
   * Insert data to the table
   *
   * @param string $table
   * @param array  $data
   * @return int
   */
  public function insert($table, $data)
  {
    if (!is_array($data))
    {
      $data = array($data);
    }

    $conditions = array();

    foreach ($data as $key => $item)
    {
      $conditions[] = '`' . $key . '` = "' . $this->escapeValue($item) . '"';
    }

    $sql = 'INSERT INTO `' . $table . '` SET ' . implode(', ', $conditions) . ';';

    if ($this->exec($sql))
    {
      return $this->affectedRows();
    }
      else
    {
      return 0;
    }
  }

  /**
   * Delete data form table
   *
   * @param string $table
   * @param array $where
   * @return int
   */
  public function delete($table, $where)
  {
    return $this->exec('DELETE FROM `' . $table . '`' . $this->where($where) . ';');
  }

  /**
   * Update data
   *
   * @param string $table
   * @param array $data
   * @param array $where
   */
  public function update($table, $data, $where)
  {
    return $this->exec('UPDATE FROM `' . $table . '`' . $this->where($where) . $this->set($data) . ';');
  }

  /**
   * Filter value
   *
   * @param string $value
   * @return string
   */
  protected function quoteValue($value)
  {
    return '\'' . str_replace(array('\'', '\\'), array('\'\'', '\\\\'),  $this->escapeValue($value)) . '\'';
  }

  /**
   * Fitler cloumn
   *
   * @param string $value
   * @return string
   */
  public function quoteColumn($value)
  {
    return '`' . $value . '`';
  }

  /**
   * Get affected rows
   *
   * @param mixed $handle
   * @return int
   */
  public function affectedRows($handle = null)
  {
    if ($handle)
    {
      return $handle->affected_rows;
    }

    return $this->connection->affected_rows;
  }

  /**
   * Last insert id
   *
   * @param mixed $handle
   * @return int
   */
  public function lastInsertId($handle = null)
  {
    if ($handle)
    {
      return $handle->affected_rows;
    }

    return $this->connection->insert_id;
  }

  /**
   * Get error text
   *
   * @return string
   */
  public function error()
  {
    return $this->connection->error;
  }

  /**
   * Get error number
   *
   * @return int
   */
   public function errno()
   {
     return $this->connection->errno;
   }

  /**
   * Get the version of MySQL Server
   *
   * @return string
   */
  public function version()
  {
    return $this->connection->server_info;
  }

  /**
   * Close the connection
   *
   * @return boolean
   */
  public function close()
  {
    return $this->connection->close();
  }

  /**
   * Filter special characters
   *
   * @param string|array $value
   * @return string|array
   */
  protected function escapeValue($value)
  {
    if (is_array($value))
    {
      foreach ($value as $k => $v)
      {
        $value[$k] = $this->connection->escape_string($v);
      }
    }
      else
    {
      $value = $this->connection->real_escape_string($value);
    }

    return $value;
  }
}
