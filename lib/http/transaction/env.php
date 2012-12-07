<?php
namespace http\transaction;
use http\Cookies;

/**
 * HTTP Environment
 * Includes data of global arrays $_SERVER, $_COOKIE
 *
 * For manual extension use the initor as follows:
 *
 *  Env::init(function($env) {
 *    # include session
 *    $env['session'] = Session::start();
 *  });
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Env implements \ArrayAccess, \Iterator {
  
  /**
   * Attribute defaults
   *
   * @static
   * @access public
   * @var array
   */
  static $defaults = array(
    
    # request defaults
    'REQUEST_METHOD' => 'GET',
    
    # server defaults
    'SERVER_NAME' => 'localhost',
    'SERVER_PORT' => 80,
    'SERVER_ADDR' => '127.0.0.1',
    
    # header defaults
    'HTTP_HOST' => 'localhost',
    'HTTP_ACCEPT' => '*/*',
    'HTTP_ACCEPT_LANGUAGE' => 'en,de',
    'HTTP_ACCEPT_ENCODING' => 'gzip',
    'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8',
    'HTTP_CONNECTION' => 'keep-alive',
    'HTTP_CACHE_CONTROL' => 'max-age=0'
  );
  
  /**
   * Initialization callback
   *
   * @static
   * @access private
   * @var callable
   */
  private static $init;
  
  /**
   * Variables container
   *
   * @access protected
   * @var array
   */
  protected $variables = array();
  
  /**
   * Stores the request info array
   *
   * @access private
   * @var array
   */
  private $request_info;
  
  /**
   * Constructs a new Env instance
   *
   * @access public
   * @param array $variables
   */
  function __construct(array $variables = array()) {
    $this->variables = array_merge(static::$defaults, $variables);
  }
  
  /**
   * Returns the environment variables as real array
   *
   * @access public
   * @return array
   */
  function variables() {
    return $this->variables;
  }
  
  /**
   * Dumps the current environment including $_SERVER and $_COOKIE globals
   *
   * @static
   * @access public
   * @return Env
   */
  static function dump() {
    $variables = $_SERVER + array(
      'cookies' => new Cookies($_COOKIE)
    );
    
    $instance = new static($variables);
    if(is_callable(self::$init)) call_user_func(self::$init, $instance);
    return $instance;
  }
  
  /**
   * Applies a initialization callback which is called
   * with the just instanciated Env instance in ::dump() method
   *
   * @static
   * @access public
   * @param callable $callback
   */
  static function init($callback) {
    self::$init = $callback;
  }
  
  /**
   * Parse request from env and returns an info array:
   * [0] => method
   * [1] => url
   * [2] => input_data
   * [3] => headerfields
   *
   * Also stores array in $this->request_info
   *
   * @access public
   * @return array
   */
  function request_info() {
    if(isset($this->request_info)) return $this->request_info;
    
    $sapi = php_sapi_name();
    
    if($sapi === 'cli') {
      $path = null;
    } else {
      $path_info = isset($this['PATH_INFO']) ? $this['PATH_INFO'] : null;
      $path = $this['SCRIPT_NAME'].$path_info;
    }
    
    $scheme = (isset($this['HTTPS']) and $this['HTTPS'] == 'on') ? 'https' : 'http';
    $host = $this['HTTP_HOST'];
    $port = $this['SERVER_PORT'];
    
    $query_string = !empty($this['QUERY_STRING']) ? $this['QUERY_STRING'] : null;
    
    if(!empty($query_string)) {
      $path = "$path?$query_string";
    }
    
    $header = array();
    foreach($this as $name => $value) {
      if(strpos($name, 'HTTP_') === 0) $header[strtolower(substr($name, 5))] = $value;
    }
    
    $input = array();
    $http_input = @file_get_contents('php://input');
    if(isset($http_input)) {
      parse_str($http_input, $input);
    }

    return $this->request_info = array($this['REQUEST_METHOD'], "$scheme://$host:$port$path", $input, $header);
  }
  
  /**
   * Returns the server name
   *
   * @access public
   * @return string
   */
  function server_name() {
    return $this->variables['SERVER_NAME'];
  }
  
  /**
   * Reads the server port
   *
   * @access public
   * @return int
   */
  function server_port() {
    return (int)$this->variables['SERVER_PORT'];
  }
  
  /**
   * Returns the attributes array
   *
   * @access public
   * @return array
   */
  function to_array() {
    return $this->variables;
  }
 
  /**
   * Iterator::rewind() implementation
   * Initializes the iteration process
   *
   * @access public
   */
  function rewind() {
    reset($this->variables);
  }

  /**
   * Iterator::current() implementation
   * Returns the current pointers value
   *
   * @access public
   * @return string
   */
  function current() {
    return current($this->variables);
  }

  /**
   * Iterator::key() implementation
   * Returns the current pointers key
   *
   * @access public
   * @return string
   */
  function key() {
    return key($this->variables);
  }

  /**
   * Iterator::next() implementation
   * After the loop body of each iteration is processed this method is called
   * Afterwards the process jumps to valid() etc. etc.
   *
   * @access public
   */
  function next() {
    next($this->variables);
  }

  /**
   * Iterator::valid() implementation
   * Called before each iteration
   * If false is returned the loop instantly breaks
   * If true is returned current() and key() gets called afterwards
   *
   * @access public
   * @return boolean
   */
  function valid() {
    return key($this->variables) !== null;
  }
  
  /**
   * ArrayAccess::offsetSet() implementation
   *
   * @access public
   * @param string $name
   * @param mixed $value
   */
  function offsetSet($name, $value) {
    $this->variables[$name] = $value;
  }

  /**
   * ArrayAccess::offsetUnset() implementation
   *
   * @access public
   * @param string $name
   */
  function offsetUnset($name) {
    unset($this->variables[$name]);
  }

  /**
   * ArrayAccess::offsetGet() implementation
   *
   * @access public
   * @param string $name
   * @return string
   */
  function offsetGet($name) {
    return $this->variables[$name];
  }

  /**
   * ArrayAccess::offsetExists() implementation
   *
   * @access public
   * @param string $name
   * @return boolean
   */
  function offsetExists($name) {
    return array_key_exists($name, $this->variables);
  }
}
?>