<?php
namespace server;
use http\Connection;

class ConnectionTest extends \test_case\Unit {
  public $script = 'http://localhost:80/transaction.php';

  
  function test_connection_establishment_and_close() {
    $con = new Connection($this->script);
    $con->establish();
    $this->assert_true($con->established());
    
    $con->close();
    $this->assert_false($con->established());
  }
  
  function test_connection_get_request() {
    $con = new Connection($this->script);
    $response = $con->request('get');
    $this->assert_eq($response->flat_body(), 'Response to GET request');
  }
  
  function test_connection_post_request() {
    $con = new Connection($this->script);
    $response = $con->request('post');
    $this->assert_eq($response->flat_body(), 'Response to POST request');
  }
  
  function test_connection_get_request_with_querystring() {
    $con = new Connection($this->script."?name=thomas");
    $response = $con->request('get');
    $this->assert_eq($response->flat_body(), 'Response to GET request with querystring: name=thomas');
  }
}
?>