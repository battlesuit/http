<?php
namespace http\transaction;
use http\Response;
use http\Request;

/**
 * Base transaction
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Base {
  
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
   * Captures a response for a given request
   *
   * @access protected
   * @param Request $request
   * @return Response
   */
  protected function capture_response(Request $request) {
    ob_start();
    try {
      $returned_response = call_user_func($this->processor, $request);
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
    
    elseif($returned_response instanceof Response) {
      $response = $returned_response;
    }
    
    elseif(is_string($returned_response)) {
      $response = new Response(200, $returned_response);
    }
        
    # if no response defined return error response
    if(!isset($response)) $response = new Response(404, "Resource not found");
    return $this->response = $response;
  }
  
  /**
   * Creates, processes and returns a transaction instance
   *
   * @static
   * @access public
   * @param callable $processor
   * @param Request $request
   * @return Base
   */
  static function run($processor, Request $request) {
    $transaction = new static($processor);
    $transaction->process($request);
    return $transaction;
  }
  
  /**
   * Processes the transaction
   * Should return a response... must! :)
   *
   * @access public
   * @param Request $request
   * @return Response
   */   
  function process(Request $request) {
    return $this->capture_response($request);
  }
}
?>