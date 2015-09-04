<?php
/**
 * Akami Core
 *
 * @author Rakume Hayashi <i@fake.moe>
 * @copyright 2015 Lingoys!Art.
 * @version 1.0
 * @package Akami
 */

namespace Akami;

class Akami
{
  /**
   * @const string
   */
  const VERSION = '1.0';

  /**
   * Errors log
   *
   * @var array
   */
  protected $error = array();

  /**
   * Library's container
   *
   * @var array
   */
  protected $container = array();

  /**
   * a simple PSR-0 autoloader
   *
   * @param string $className className of the file
   */
  static public function autoload($className)
  {
    // Trim `\` from $className
    $className = ltrim($className, '\\');

    // The base dir
    $fileName = dirname(__DIR__) . DIRECTORY_SEPARATOR;

    // When it has namespace
    if ($lastNamespacePos = strripos($className, '\\'))
    {
      $namespace = substr($className, 0, $lastNamespacePos);
      $className = substr($className, $lastNamespacePos + 1);
      $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    // When exists the file
    if (file_exists($fileName))
    {
      require $fileName;
    }
  }

  /**
   * Register Slim's PSR-0 autoloader
   *
   * @return void
   */
  static public function registerAutoloader()
  {
    spl_autoload_register(__NAMESPACE__ . '\\Akami::autoload');
  }

  /**
   * Constructor Akami
   *
   * @return \Akami\Akami
   */
  public function __construct($config = array())
  {
    set_error_handler(array($this, 'handleErrors'));
    set_exception_handler(array($this, 'handleException'));

    // New router
    $this->container['router'] = new \Akami\Router;

    return $this;
  }

  /**
   * Add route
   *
   * @param string $method
   * @param string $route
   * @param mixed  $callback
   * @return \Akami\Akami
   */
  public function add($method, $route, $callback)
  {
    $this->container['router']->add($method, $route, $callback);

    return $this;
  }

  /**
   * Add get route
   *
   * @param string $route
   * @param mixed  $callback
   * @return \Akami\Akami
   */
  public function get($route, $callback)
  {
    return $this->add('GET', $route, $callback);
  }

  /**
   * Add post route
   *
   * @param  string $route
   * @param  mixed  $callback
   * @return \Akami\Akami
   */
  public function post($route, $callback)
  {
    return $this->add('POST', $route, $callback);
  }

  /**
   * Add put route
   *
   * @param  string $route
   * @param  mixed  $callback
   * @return \Akami\Akami
   */
  public function put($route, $callback)
  {
    return $this->add('PUT', $route, $callback);
  }

  /**
   * Add delete route
   *
   * @param  string $route
   * @param  mixed  $callback
   * @return \Akami\Akami
   */
  public function delete($route, $callback)
  {
    return $this->add('DELETE', $route, $callback);
  }

  /**
   * Configure Akami Settings
   *
   * @param  string|array $name
   * @param  mixed        $value
   * @return mixed
   */
  public function config($name, $value = null)
  {
    // When $name is array
    if (is_array($name))
    {
      // When $value is true
      if (true === $value)
      {
        $this->settings = array_merge_recursive($this->settings, $name);
      }
        else
      {
        $this->settings = array_merge($this->settings, $name);
      }
    }
      // When $name is string
      else if (func_num_args() === 1 && is_string($name))
    {
      return isset($this->settings[$name]) ? $this->settings[$name] : null;
    }
      else
    {
      $this->settings[$name] = $value;
    }

    return $this;
  }

  /**
   * Run the app
   *
   * @return void
   */
  public function run($routeUrl = '')
  {
    // Route the url
    $this->container['router']->route($routeUrl);

    restore_error_handler();
    restore_exception_handler();
  }

  /**
   * Convert errors into ErrorException objects
   *
   * @param int    $errno The numeric type of the error
   * @param string $str   The error Message
   * @param string $file  The path to the affected file
   * @param string $line  The line of the affected file
   * @throws \ErrorException
   */
  public function handleErrors($errno, $str = '', $file = '', $line = '')
  {
    // When it cannot catch the error
    if (!($errno && error_reporting()))
    {
      return;
    }

    throw new \ErrorException($str, $errno, 0, $file, $line);
  }

  /**
   * Handle with exceptions
   *
   * @param exception $e
   */
  public function handleException($e)
  {
    $msg = '';
    $trace = $e->getTrace();
    ksort($trace);

    foreach ($trace as $error)
    {
      if (isset($error['function']) && isset($error['file']))
      {
        $msg .= $error['file'] . '&nbsp;(' . $error['line'] . ') ';

        if (isset($error['function']) && is_string($error['function']))
        {
          $msg .= (isset($error['class']) ? $error['class'] . $error['type'] : '') . $error['function'] . '()';
        }

        $msg .= '<br />';
      }
    }

    echo <<<EOT
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Akami Framework</title>
</head>
<body>
    <style>
        html, body {
            height: 100%;
            padding: 0;
            margin: 0;
        }

        body {
            width: 100%;
            display: table;

            background: #16938A;
            color: #333;
            font-size: 14px;
            line-height: 1.825;
            font-family: "Lucida Grande", Helvetica, Arial, "Microsoft YaHei", FreeSans, Arimo, "Droid Sans","wenquanyi micro hei","Hiragino Sans GB", "Hiragino Sans GB W3", Arial, sans-serif
        }

        .box {
            display: table-cell;
            vertical-align: middle;
        }

        .box .container {
            background: #fff;
            width: 500px;
            margin: 0 auto;
            padding: 2em;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
            -webkit-box-sizing: border-box;
                    box-sizing: border-box;
        }

        header {
          color: #999;
          display: block;
          margin-bottom: 1em;
        }

        .bold {
          color: #222;
          font-weight: bold;
        }

        p {
            color: #222;
        }

        .message {
          color: #777;
          font-size: 12px;

          margin-top: 1em;
          margin-bottom: 0;
        }
    </style>
    <div class="box">
        <div class="container">
            <header>
              <span class="bold">Message Reminder</span>
               - {$e->getMessage()}
            </header>
            <p class="file"><span class="bold">Location: </span> {$e->getfile()} <i>({$e->getLine()})</i></p>
            <p class="message">{$msg}</p>
        </div>
    </div>
</body>
</html>
EOT;
  }

  /**
   * Get application instance
   *
   * @return \Akami\Akaimi
   */
  static public function getInstance()
  {
    return $this;
  }
}
