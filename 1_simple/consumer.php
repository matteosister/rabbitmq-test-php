<?php
/**
 * User: matteo
 * Date: 17/11/13
 * Time: 12.05
 * Just for fun...
 */


require_once __DIR__.'/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPConnection;

$host = 'localhost';
$port = 5672;

$conn = new AMQPConnection($host, $port, 'guest', 'guest');
$channel = $conn->channel();

//$queue = $channel->queue_declare('simple', false, false);
$channel->exchange_declare('simple', 'fanout', false, false, false);
list($queue_name,,) = $channel->queue_declare("", false, false, true, false);

$channel->queue_bind($queue_name, 'simple');

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
  echo ' [x] ', $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
