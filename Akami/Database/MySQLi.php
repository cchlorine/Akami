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

  public function close()
  {
    $this->connection = mysqli_close($this->connection);
  }
}
