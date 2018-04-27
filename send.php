<?php
/**
 * Created by PhpStorm.
 * User: atom
 * Date: 2018/4/16
 * Time: 下午5:06
 */

require_once __DIR__ . "/vendor/autoload.php";

//use PhpAmqpLib\Connection\AMQPStreamConnection;
//use PhpAmqpLib\Message\AMQPMessage;

$connection = new \PhpAmqpLib\Connection\AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

//var_dump($channel);

//$channel->queue_declare('task_queue', false , true, false, false, '', '');

//var_dump($argv);
$routing_key = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : "backend.shipment";//<front|backend><register|reset_password|shipping|arriving>

$receiver_array = array_slice($argv, 3);
if (empty($receiver_array)) {
    $data = array('mail_key' => $routing_key, 'type' => $argv[2], 'to' => array('1187259952@qq.com'));
} else {
    $data = array('mail_key' => $routing_key, 'type' => $argv[2], 'to' => $receiver_array);
}


$channel->exchange_declare('mails_topic', 'topic', false , true, false);

$msg = new \PhpAmqpLib\Message\AMQPMessage(
    json_encode($data),
    array('delivery_mode' => \PhpAmqpLib\Message\AMQPMessage::DELIVERY_MODE_PERSISTENT)
);

$channel->basic_publish($msg, 'mails_topic', $routing_key);

echo " [x] Sent '" . json_encode($data) . "\n";

$channel->close();
$connection->close();