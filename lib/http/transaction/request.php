<?php
namespace http\transaction;

class Request extends \http\Request {
  
  /**
   * Contains the transaction environment
   * http\Env acts like an array and if its not given $env stays a normal array 
   *
   * @access public
   * @var Env or array
   */
  public $env = array();
  
  function __construct(Env $env = null) {
    if(isset($env)) $this->apply_env($env);
  }
  
  /**
   * Applies a environment
   *
   * @access public
   * @param Env $env
   */
  function apply_env(Env $env) {
    $this->env = $env;
    list($method, $url, $input, $fields) = $this->parse($env);
    parent::__construct($method, $url, $input, $fields);
  }
  
  /**
   * Parsed request info from env and returns an array:
   * [0] => method
   * [1] => url
   * [2] => input_data
   * [3] => headerfields
   *
   * @access protected
   * @return array
   */
  protected function parse(Env $env) {
    $sapi = php_sapi_name();
    
    if($sapi === 'cli') {
      $path = null;
    } else {
      $path_info = isset($env['PATH_INFO']) ? $env['PATH_INFO'] : null;
      $path = $env['SCRIPT_NAME'].$path_info;
    }
    
    $scheme = (isset($env['HTTPS']) and $env['HTTPS'] == 'on') ? 'https' : 'http';
    $host = $env['HTTP_HOST'];
    $port = $env['SERVER_PORT'];
    
    $query_string = !empty($env['QUERY_STRING']) ? $env['QUERY_STRING'] : null;
    
    if(!empty($query_string)) {
      $path = "$path?$query_string";
    }
    
    $header = array();
    foreach($env as $name => $value) {
      if(strpos($name, 'HTTP_') === 0) $header[strtolower(substr($name, 5))] = $value;
    }
    
    $input = array();
    $http_input = @file_get_contents('php://input');
    if(isset($http_input)) {
      parse_str($http_input, $input);
    }
    
    return array($env['REQUEST_METHOD'], "$scheme://$host:$port$path", $input, $header);
  }
}
?>