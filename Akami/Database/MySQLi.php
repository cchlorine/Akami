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
   * Class construction
   *
   * @return void
   */
  public function __construct()
  {
    return;
  }

  /**
   * Connect to the database
   *
   * @return boolean
   */
  public function connect()
  {
    if ($this->connection)
    {
      $this->connection = mysqli_close($this->connection);
    }

    $this->connection = mysqli_connect($this->config['hostname'], $this->config['username'], $this->config['password'], $this->config['database']);

    if (mysqli_connect_error())
    {
      $this->error = 'Cannot connect to server: (#' . mysqli_connect_errno() .') ' . mysqli_connect_error();
      $this->errno = mysqli_connect_errno();

      return false;
    }

    if (!mysqli_set_charset($this->connection, $this->config['charset']))
    {
      $this->error = 'Cannot set charset: ' . mysqli_error($this->connection);
      $this->errno = mysqli_errno($this->connection);

      return false;
    }

    return true;
  }

  /**
   * Check the availability of the database connection
   *
   * @return object
   */
  public function check()
  {
    if (empty($this->connection) || !mysqli_ping($this->connection))
    {
      $this->connect();
    }

    return $this->connection;
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

    return mysqli_query($this->connection, $this->query);
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

    if ($this->result)
    {
      if (@mysqli_num_rows($result) > 0)
      {
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
          array_push($data, $row);
        }

        mysqli_free_result($result);
      }
        elseif (preg_match('/^select/i', trim($sql)))
      {
        return null;
      }
        else
      {
        return true;
      }
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
    if (is_array($data))
    {
      $data = array($data);
    }

    $conditions;

    foreach ($data as $key => $item)
    {
      $conditions[] = '`' . $key . '` = "' . $this->escape_value($item) . '"';
    }

    $sql = 'INSERT INTO `' . $table . '` SET ' . implode(', ', $conditions) . ';';

    if ($this->exec($sql))
    {
      return mysqli_insert_id($this->connection);
    }
      else
    {
      return 0;
    }
  }

  public function close()
  {
    $this->connection = mysqli_close($this->connection);
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
				$value[$k] = call_user_func('escape_value', $value[$k]);;
			}
		}
      else
    {
			$value = mysqli_real_escape_string($this->connection, $value);
		}

		return $value;
	}

}
