<?php
namespace http;

class ControllerMock extends TransactionController {
  function process_transaction(Request $request) {
    return new Response(200, 'Easy response');
  }
}
?>