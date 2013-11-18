<?php
/**
 * User: matteo
 * Date: 18/11/13
 * Time: 23.31
 * Just for fun...
 */


require_once __DIR__.'/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPConnection;

$host = 'localhost';
$port = 5672;

$conn = new AMQPConnection($host, $port, 'guest', 'guest');
$channel = $conn->channel();

$channel->exchange_declare('simple', 'fanout', false, false, true);

while (true) {
    $msg = new \PhpAmqpLib\Message\AMQPMessage(mt_rand(1, 10000));
    $channel->basic_publish($msg, 'simple');
    echo ' [x] ', $msg->body, "\n";
    usleep(rand(300000, 1000000));
}
