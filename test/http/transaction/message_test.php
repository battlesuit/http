<?php
namespace http\transaction;

class MessageTest extends MessageTester {
  function set_up() {
    parent::set_up();
    
    $this->load_mocks('message');
    $this->message = new MessageMock();
  }
  
  function test_defaults() {
    $this->assert_empty_array($this->message->fields());
  }
  
  function test_field_reader_without_any_fields_applied() {
    $this->assert_thrown_exception($this->message, 'read_field');
  }
  
  function test_field_exists() {
    $this->assert_false($this->message->field_exists('location'));
  }
  
  function test_field_writer() {
    $this->message->write_field('Content-Type', 'application/js');
    $this->assert_true($this->message->field_exists('content_type'));
  }
  
  function test_field_accessor() {
    $this->message->field('content-type', 'text/plain');
    $this->assert_true($this->message->field_exists('content_type'));
    $this->assert_eq($this->message->field('content_type'), 'text/plain');
  }
  
  function test_content_type_without_charset() {
    $this->message->content_type('text/html');
    $this->assert_true($this->message->field_exists('content_type'));
    $this->assert_eq($this->message->field('content_type'), 'text/html; charset=utf-8');
  }
  
  function test_content_type_with_charset() {
    $this->message->content_type('text/css', 'iso-8859-1');
    $this->assert_eq($this->message->field('content_type'), 'text/css; charset=iso-8859-1');
  }
  
  function test_iteration() {
    foreach($this->test_header as $name => $value) {
      $this->message->write_field($name, $value);
    }
    
    foreach($this->message as $field_name => $field_value) {
      $this->assert_key_exists($field_name, $this->test_header);
      $this->assert_includes($field_value, $this->test_header);
    }
  }
  
  function test_array_access() {
    foreach($this->test_header as $name => $value) {
      $this->message[$name] = $value;
    }
    
    $this->assert_eq($this->message['host'], 'domain.de');
    $this->assert_eq($this->message['accept'], 'text/xml,text/plain,application/*,*/javascript');
  }
  
  function test_write_fields() {
    $this->message->fields(array('Content-Type' => 'text/html; charset=utf8', 'Accept-Language' => 'de,en'));
    $this->assert_eq($this->message['content_type'], 'text/html; charset=utf8');
    $this->assert_eq($this->message['accept_language'], 'de,en');
  }
}
?>