<?php
namespace http;

class MessageTester extends TestCase {
  function set_up() {
    $this->test_header = array(
      'host' => 'domain.de',
      'accept' => 'text/xml,text/plain,application/*,*/javascript',
      'accept_encoding' => 'gzip',
      'accepted_language' => 'de,en',
      'accepted_charset' => 'ISO-8859-1,utf-8',
      'connection' => 'keep-alive',
      'cache_control' => 'max-age=0',
      'user_agent' => null
    );
  }
}
?>