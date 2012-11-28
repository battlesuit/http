<?php
namespace http\transaction\middleware;
use http\transaction\Request;
use http\transaction\Response;
use http\transaction\Base;

/**
 * Exception catcher middleware
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class ShowExceptions extends Base {
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