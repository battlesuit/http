<?php
namespace server;
use http\Connection;

class ConnectionTest extends \test_case\Unit {
  public $host = 'localhost';

  
  function test_connection_establishment_and_close() {
    $con = new Connection($this->host);
    $con->establish();
    $this->assert_true($con->established());
    
    $con->close();
    $this->assert_false($con->established());
  }
  
  function test_connection_get_request() {
    $con = new Connection($this->host);
    $response = $con->request('/transaction.php');
    $this->assert_eq($response->flat_body(), 'Response to GET request');
  }
  
  function test_connection_post_request() {
    $con = new Connection($this->host);
    $response = $con->request('/transaction.php', 'post');
    $this->assert_eq($response->flat_body(), 'Response to POST request');
  }
  
  function test_connection_get_request_with_querystring() {
    $con = new Connection($this->host);
    $response = $con->request("/transaction.php?name=thomas", 'get');
    $this->assert_eq($response->flat_body(), 'Response to GET request with querystring: name=thomas');
  }
}
?>