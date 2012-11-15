<?php
namespace http;

class ResponseTest extends MessageTester {
  function test_blank_construction() {
    new Response();
  }
  
  function test_defaults() {
    $response = new Response();
    $this->assert_eq($response->status, 200);
    $this->assert_eq($response->protocol, 'HTTP/1.0');
    $this->assert_empty_array($response->body());
    $this->assert_empty_string($response->flat_body());
  }
  
  function test_to_string() {
    $response = new Response(201, array("Created"));
    $this->assert_equality("$response", 'Created');
  }
  
  function test_location_accessor() {
    $response = new Response();
    $this->assert_equality($response->location('http://redirect.to/path'), 'http://redirect.to/path');
    
    $this->assert_equality($response->location(), 'http://redirect.to/path');
  }
  
  function test_body_accessor() {
    $response = new Response();
    $body = $response->body(array('hello world'));
    $this->assert_equality($body[0], 'hello world');
    $body = $response->body();
    $this->assert_equality($body[0], 'hello world');
  }
  
  function test_body_accessor_passing_a_string() {
    $response = new Response();
    $body = $response->body('hello world');
    $this->assert_equality($body[0], 'hello world');
    $body = $response->body();
    $this->assert_equality($body[0], 'hello world');
  }
  
  function test_flat_body() {
    $response = new Response(200, array('simon says'));
    $this->assert_equality($response->flat_body(), 'simon says');
  }
  
  function test_to_array() {
    $response = new Response(404, 'Resource not found', array('location' => 'http://redirect.to/path', 'content_type' => 'text/plain; charset=utf8'));
    $this->assert_equality($response->to_array(), array(404, array('location' => 'http://redirect.to/path', 'content_type' => 'text/plain; charset=utf8'), array('Resource not found')));
  }
}
?>