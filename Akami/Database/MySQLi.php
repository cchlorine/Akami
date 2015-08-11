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

    $this->connect();
  }

  /**
   * Connect to the mysqli
   *
   * @return \MySQLi
   */
  protected function connect()
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
   * @param string $query
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
   * @param string $query
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
   * @param string $where
   * @return string
   */
  protected function where($where)
  {
    $clause = '';

    if (is_array($where))
    {
      //
    }
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
   * Delete data form table
   *
   * @return int
   */
  public function delete($table, $where)
  {
    return $this->exec('DELETE FROM `' . $table . '`' . $this->where($whre));
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

  /**
   * Get the version of MySQL Server
   *
   * @return string
   */
  public function version()
  {
    return $this->mysqli->server_info;
  }

  /**
   * Close the connection
   */
  public function close()
  {
    $this->mysqli->close();
  }
}
