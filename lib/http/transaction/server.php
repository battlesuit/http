<?php
namespace http\transaction;
use http\Request;

/**
 * Server transaction
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Server extends Base {
  
  /**
   * Middleware to include
   *
   * @access protected
   * @var array
   */
  protected $middleware = array(
    'http\transaction\middleware\ShowExceptions'
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
   * Creates, processes and returns a transaction instance
   *
   * @static
   * @access public
   * @param callable $processor
   * @param Request $request
   * @return Local
   */
  static function run($processor, Request $request) {
    $transaction = new static($processor);
    if(is_callable(self::$init)) call_user_func(self::$init, $transaction);
    $transaction->process($request);
    return $transaction;
  }
  
  /**
   * Defines a initalization callback which takes the transaction instance
   * after creation by static::run()
   *
   * @static
   * @access public
   * @param callable $initalization_callback
   */
  static function init($initalization_callback) {
    self::$init = $initalization_callback;
  }
  
  /**
   * Processes the transaction
   *
   * @access public
   * @param Request $request
   * @return Response
   */   
  function process(Request $request) {
    $application = $this->processor;
    foreach($this->prepare_middleware() as $app) $application = call_user_func($app, $application);
    $this->processor = $application;
    return parent::process($request);
  }
  
  /**
   * Integrates a new middleware class
   * Every class instance must be invocable. Normally you have to extend transaction\Base
   *
   * @access public
   * @param string $class
   */
  function integrate($class) {
    $this->middleware[] = $class;
  }
  
  /**
   * Prepares transaction middleware for execution
   * 
   * @access public
   * @return array
   */
  function prepare_middleware() {
    $middleware = array();
    
    foreach($this->middleware as $class) {
      $middleware[] = function($application) use($class) {
        return new $class($application);
      };
    }
    
    return array_reverse($middleware);
  }
  
  /**
   * Writes all headers and prints the response body
   *
   * @access public
   */
  function serve() {
    $response = $this->response;
    
    # introduce response
    header("$response->protocol $response->status");
    
    if($response->any_fields()) {
      foreach($response as $name => $value) {
        $name = str_replace(' ', '-', ucwords(str_replace('_', ' ', $name)));
        header("$name: $value");
      }
    }
    
    print $response->flat_body();
  }
}
?>