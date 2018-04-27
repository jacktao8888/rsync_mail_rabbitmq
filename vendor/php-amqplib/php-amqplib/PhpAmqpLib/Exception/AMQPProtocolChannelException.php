<?php
namespace PhpAmqpLib\Exception;

class AMQPProtocolChannelException extends AMQPProtocolException
{
    public function __construct($reply_code, $reply_text, array $method_sig) { parent::__construct($reply_code, $reply_text, $method_sig); }
}
