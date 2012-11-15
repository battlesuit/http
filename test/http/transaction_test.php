<?php
namespace http;

class TransactionTest extends TestCase {
    
  function assert_flat_body_eq($processor, $request, $value) {
    $this->assert_eq(Transaction::handle($processor, $request)->flat_body(), $value);
  }
  
  function set_up() {
    $this->not_found_responder = function() {
      return new Response(404, 'Ooops! Not found', array('content_type' => 'text/plain'));
    };
    
    $self = $this;
    $this->assert_arguments_responder = function($request) use($self) {
      $self->assert_instanceof($request, 'http\Request');

    };
  }
  
  function test_process() {
    $transaction = new Transaction($this->not_found_responder);
    $response = $transaction->process(new Request());
    $this->assert_instanceof($response, 'http\Response');
    $this->assert_eq("$response", 'Ooops! Not found');
  }
  
  function test_procssor_arguments() {
    $transaction = new Transaction($this->assert_arguments_responder);
    $transaction->process(new Request());
  }
  
  function test_invocation() {
    $transaction = new Transaction($this->not_found_responder);
    $response = $transaction(new Request(), array());
    $this->assert_instanceof($response, 'http\Response');
    $this->assert_eq("$response", 'Ooops! Not found');
  }
  
  function test_static_handle() {
    $response = Transaction::handle($this->not_found_responder, new Request());
    $this->assert_instanceof($response, 'http\Response');
    $this->assert_eq("$response", 'Ooops! Not found');
  }
  
  function test_processor_status_change() {
    $processor = function($request) {
      return new Response(404);
    };
    
    $response = Transaction::handle($processor, new Request());
    $this->assert_eq($response->status, 404);
  }
  
  function test_processor_echo() {
    $processor = function($request) {
      echo "hello world";
    };
    
    $this->assert_flat_body_eq($processor, new Request(), 'hello world');
  }
  
  function test_processor_creates_new_response() {
    $processor = function($request) {
      return new Response(200, 'can you believe <b>that</b>', array('content_type' => 'text/html'));
    };
    
    $response = Transaction::handle($processor, new Request());
    $this->assert_eq($response->status, 200);
    $this->assert_eq($response->flat_body(), 'can you believe <b>that</b>');
    $this->assert_eq($response['content_type'], 'text/html');
  }
  
  function test_processor_responds_with_array() {
    $processor = function($request) {
      return array(404, array('content_type' => 'text/html'), array('Not found'));
    };
    
    $response = Transaction::handle($processor, new Request());
    $this->assert_eq($response->status, 404);
    $this->assert_eq($response->flat_body(), 'Not found');
    $this->assert_eq($response['content_type'], 'text/html');
  }
}
?>