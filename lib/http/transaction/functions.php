<?php
namespace http\transaction {
  use http\Request;

  /**
   * HTTP environment accessor
   *
   * Example:
   *  env(); # => only returns the environment
   *  env('PATH_INFO'); # => returns the environment variable PATH_INFO
   *  env('REQUEST_URI', '/my/path'); # => sets the environment variable REQUEST_URI to /my/path
   *
   * @param string $name
   * @param mixed $value
   * @return Env
   */
  function env($name = null, $value = null) {
    static $instance;
    if(!isset($instance)) $instance = Env::dump();
    
    if(isset($name)) {
      if(isset($value)) {
        return $instance[$name] = $value;
      } else return $instance[$name];
    }
    
    return $instance;
  }
  
  /**
   * Stores and returns the request instance for the current transaction
   * 
   * @return Request
   */
  function request() {
    static $instance;
    if(!isset($instance)) $instance = new Request(env());
    return $instance;
  }
  
  /**
   * Starts a server transaction within a given block
   *
   * @param callable $processor
   * @param Request $request
   * @return Server
   */
  function run($processor, Request $request = null) {
    return Server::run($processor, isset($request) ? $request : request());
  }
}
?>