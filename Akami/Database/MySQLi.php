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
    foreach ($config as $key => $value)
    {
      if (in_array($key, $this->config))
      {
        $this->config[$key] = $value;
      }
    }
  }

  /**
   * Connect to the mysqli
   *
   * @return \MySQLi
   */
  public function connect()
  {
    return $this->mysqli = new \MySQLi($this->config['hostname'], $this->config['username'], $this->config['password'], $this->config['database']);
  }

  /**
   * Check the availability of the database connection
   *
   * @return \MySQLi
   */
  public function check()
  {
    if (empty($this->mysqli) || !$this->mysqli->ping())
    {
      $this->connect();
    }

    return $this->mysqli;
  }

  /**
   * Excute query
   *
   * @return array
   */
  public function exec($query)
  {
    $this->check();
    array_push($this->logs, $query);

    return $this->mysqli->query($query);
  }

  /**
   * Query
   *
   * @return array
   */
  public function query($query)
  {
    $data = array();
    $result = $this->exec($query);

    if ($this->mysqli->affected_rows > 0)
    {
      $data = $result->fetch_all();
      $result->free();
    }
      elseif (preg_match('/^select/i', trim($query)))
    {
      return null;
    }
      else
    {
      return true;
    }

    return $data;
  }

  public function select()
  {
    //
  }

  public function delete($table, $where)
  {
    //
  }

  /**
   * Insert data
   *
   * @param string $table
   * @param array  $data
   * @return array|int
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
      $conditions[] = '`' . $key . '` = "' . $this->escape_value($item) . '"';
    }

    $sql = 'INSERT INTO `' . $table . '` SET ' . implode(', ', $conditions) . ';';

    if ($this->exec($sql))
    {
      return $this->mysqli->affected_rows;
    }
      else
    {
      return 0;
    }
  }

  /**
	 * Filter special characters
	 *
	 * @param string|array $value
	 * @return string|array
	 */
	protected function escape_value($value)
	{
		if (is_array($value))
    {
			foreach ($value as $k => $v)
			{
				$value[$k] = $this->mysqli->escape_string($v);
			}
		}
      else
    {
			$value = $this->mysqli->escape_string($value);
		}

		return $value;
	}

  public function close()
  {
    $this->mysqli->close();
  }
}
