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

$channel->exchange_delete('simple');
$channel->exchange_declare('simple', 'fanout', false, false, true);
$channel->queue_declare('simple_queue', false, true, false, false);

$channel->queue_bind('simple_queue', 'simple');

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function(\PhpAmqpLib\Message\AMQPMessage $msg) {
    echo ' [x] ', $msg->body, "\n";
    usleep(200000);
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_consume('simple_queue', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
