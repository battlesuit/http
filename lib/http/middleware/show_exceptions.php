<?php
namespace http\middleware;
use http\Request;
use http\Response;
use http\Transaction;

/**
 * Exception catcher middleware
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class ShowExceptions extends Transaction {
  function process(Request $request) {
    try {
      $response = call_user_func($this->processor, $request);
    } catch(\Exception $exception) {
      return new Response(500, "<pre>$exception</pre>", array('content_type' => 'text/html'));
    }
    
    return $response;
  }
}
?>