<?php
namespace http;

/**
 * Mixins transaction abilities
 *
 * PHP Version 5.4+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Module extends Object {
  
  /**
   * Available transaction adapters
   *
   * @static
   * @access public
   * @var array
   */
  static $transactors = array(
    'cgi' => 'http\cgi\Transactor'
  );
  
  /**
   * Middleware to include
   *
   * @static
   * @access public
   * @var array
   */
  static $transaction_middleware = array(
    'http\middleware\ShowExceptions'
  );
  
  /**
   * Runs a transaction application and returns a response
   *
   * @static
   * @access public
   * @param string $type
   * @param callable $application
   * @return Transactor
   */
  static function run_transaction($application, $type_or_request = 'cgi') {        
    $middleware = static::prepare_middleware();
    
    foreach($middleware as $app) $application = $app($application);
    
    if($type_or_request instanceof Request) {
      $request = $type_or_request;
      $type = 'cgi';
    } else $type = $type_or_request;
    
    if(isset(self::$transactors[$type])) {
      $transactor_class = self::$transactors[$type];
    } else trigger_error("Transactor for $type is not registered", E_USER_ERROR);
      
    if(!isset($request)) $request = $transactor_class::new_request();

    
    ob_start();
    $response = $application($request);
    $output = ob_get_clean();
    
    if(!($response instanceof Response)) {
      $response = new Response();
    }
    
    if(!empty($output)) {
      $response->body($output);
    }
    
    $responder = new $transactor_class($response);
    return $responder;
  }
  
  /**
   * Prepares transaction middleware for execution
   * 
   * @static
   * @access public
   * @return array
   */
  static function prepare_middleware() {
    $middleware = array();
    foreach(static::$transaction_middleware as $class) {
      $middleware[] = function($application) use($class) {
        return new $class($application);
      };
    }
    return array_reverse($middleware);
  }
}
?>