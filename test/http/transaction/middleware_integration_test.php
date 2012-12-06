<?php
namespace http\transaction;
use http\Response;
use http\Request;

class MiddlewareIntegrationTest extends \http\TestUnit {
  function boot_up() {
    require_once $this->bench_dir()."/middleware/info_middleware.php";
    
    Server::Middleware(function($mw) {
      $mw->integrate('InfoMiddleware');
    });
  }
  
  function test_transaction() {
    $processor = function($request) {
      return new Response(200, 'Poooh...');
    };
    
    $response = Server::run($processor, new Request('http://localhost', 'delete'))->response();
    $this->assert_eq("$response", 'Poooh...Info: DELETE http://localhost');
  }
  
  function shut_down() {
    Server::Middleware()->reset();
  }
}
?>