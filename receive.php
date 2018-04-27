<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/mail.php";
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

//$channel->queue_declare('task_queue', false, true, false, false);

list($queue_name,,) = $channel->queue_declare("", false, true, true, false);

//$routing_keys = array_slice($argv, 1);
//
//foreach ($routing_keys as $routing_key) {
//    $channel->queue_bind($queue_name, 'mails_topic', $routing_key);
//}

$routing_key = !empty($argv[1]) ? $argv[1] : 'backend.shipment';
$channel->queue_bind($queue_name, 'mails_topic', $routing_key);

echo ' [*] Waiting for messages.To exit press CTRL+C', "\n";

//print_r($channel);

$callback = function ($msg) {
    echo " [x] Received " . $msg->body, "\n";
//    sleep(substr_count($msg->body, '.'));
//    echo " [x] Done" . "\n";
//    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

    $mail_text = array(
        'backend.shipment' => array(
            0 => 'Prepare',
            1 => 'Packaging',
            2 => 'Shipping',
            3 => 'Arrived'
        ),
        'front.payment' => array(
            0 => 'Pay Cancelled',
            1 => 'Pay Failed',
            2 => 'Pay Successful',
        ),
    );

    $message = json_decode($msg->body, true);
    if (!empty($message['to'])) {
        !is_array($message['to']) && $message['to'] = array($message['to']);
        $mail = new Mail();
        if (!$mail->send($message['to'], $message['mail_key'], $mail_text[$message['mail_key']][$message['type']])) {
            $fp = fopen('send_err.log', 'a+');
            fwrite($fp, date('Y-m-d H:i:s') . ": " . json_encode($message['to']));
            fclose($fp);
        }
    }
};

$channel->basic_qos(null, 1, null);//Fair Dispatch
$channel->basic_consume($queue_name, '',  false, false, false, false, $callback);

//var_dump($channel->callbacks);

while (count($channel->callbacks)) {
    $channel->wait();
}