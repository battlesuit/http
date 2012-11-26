<?php
namespace http\cgi;

/**
 * CGI Request
 * Only works with cgi environments
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Request extends \http\Request {
  public $env;
  
  final function __construct(Env $env) {
    $this->initialize($env);
  }

  /**
   * Builds a new request instance with the given transaction informations
   * 
   * @access protected
   */
  protected function initialize($env) {
    $this->env = $env;
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

    parent::__construct($env['REQUEST_METHOD'], "$scheme://$host:$port$path", $input, $header);
  }
  
  function xhr() {
    return isset($this->env['HTTP_X_REQUESTED_WITH']) and $this->env['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
  }
}
?>