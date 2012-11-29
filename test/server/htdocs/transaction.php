<?php
namespace http {
  require __DIR__."/../../../../module.php";
  import('http');
  
  $processor = function($request) {
    if($request->query_string() !== null) {
      return new Response(200, "Response to GET request with querystring: ".$request->query_string());
    }
    
    if($request->via_get()) {
      return new Response(200, "Response to GET request");
    }
    
    if($request->via_post()) {
      return new Response(200, "Response to POST request");
    }
    
  };
  
  # the $router variable is invocable so we can pass it to transaction\run()
  transaction\run($processor)->serve();
}
?>