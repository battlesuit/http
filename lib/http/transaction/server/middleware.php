<?php
namespace http\transaction\server;

/**
 * Middleware container
 * Used as a singleton in http\transaction\Server::Middleware()
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Middleware {
  
  /**
   * Middleware stack
   *
   * @access protected
   * @var array
   */
  private $stack = array();
  
  /**
   * Empties the middleware stack
   *
   * @access public
   */
  function reset() {
    $this->stack = array();
  }
  
  /**
   * Integrates a new middleware class
   * Every class instance must be invocable. Normally you have to extend http\transaction\Application
   *
   * @access public
   * @param string $class
   */
  function integrate($class) {
    $this->stack[] = $class;
  }
  
  /**
   * Compose all middleware into one processor
   *
   * @access public
   * @param callable $processor
   * @return callable
   */
  function compose($processor) {
    foreach($this->prepare() as $p) $processor = call_user_func($p, $processor);
    return $processor;
  }
  
  /**
   * Prepares transaction middleware for execution
   * 
   * @access public
   * @return array
   */
  function prepare() {
    $middleware = array();
    
    foreach($this->stack as $class) {
      $middleware[] = function($application) use($class) {
        return new $class($application);
      };
    }
    
    return array_reverse($middleware);
  }
}
?>