<?php
namespace http;

/**
 * Default transaction handler
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Transaction {
  
  /**
   * Middleware to include
   *
   * @static
   * @access public
   * @var array
   */
  protected $middleware = array(
    'http\middleware\ShowExceptions'
  );
  
  /**
   * Process callback
   *
   * @access protected
   * @var callable
   */
  protected $processor;
  
  /**
   * Processed response
   *
   * @access protected
   * @var Response
   */
  protected $response;
  
  /**
   * Constructs a new transaction instance
   *
   * @access public
   * @param callable $processor
   */
  function __construct($processor) {
    $this->processor = $processor;
  }
  
  /**
   * Invocation processes a transaction
   *
   * @access public
   * @param Request $request
   * @return Response $response
   */  
  function __invoke(Request $request) {
    return $this->process($request);
  }
  
  /**
   * Reads the transaction processor
   *
   * @access public
   * @return callable
   */
  function processor() {
    return $this->processor;
  }
  
  /**
   * Returns the response set by process()
   *
   * @access public
   * @return Response
   */
  function response() {
    return $this->response;
  }
  
  /**
   * Runs a full process statically
   *
   * @static
   * @access public
   * @param callable $processor
   * @param Request $request
   * @return Response
   */
  static function run($processor, Request $request) {
    $transaction = new static($processor);
    $transaction->process($request);
    return $transaction;
  }
  
  /**
   * Process transaction
   *
   * @access public
   * @param Request $request
   * @return Response $response
   */   
  function process(Request $request) {
    $application = $this->processor;
    foreach($this->prepare_middleware() as $app) $application = $app($application);
    
    ob_start();
    try {
      $returned_response = call_user_func($application, $request);
    } catch(\Exception $e) {
      ob_end_clean();
      throw $e;
    }
    $captured_response = ob_get_clean();
    
    
    if(!empty($captured_response)) {
      $response = new Response(200, $captured_response);
    }
    elseif(is_array($returned_response)) {
      $response = new Response($returned_response[0], $returned_response[2], $returned_response[1]);
    }
    elseif($returned_response instanceof Response) $response = $returned_response;
    elseif(is_string($returned_response)) {
      $response = new Response(200, $returned_response);
    }
    
    if(!isset($response)) $response = new Response();
    
    return $this->response = $response;
  }
  
  /**
   * Prepares transaction middleware for execution
   * 
   * @static
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