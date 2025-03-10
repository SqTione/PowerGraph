<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueProducer {
    private $connection;
    private $channel;

    public function __construct() {
        $config = require dirname(__FILE__) . '/../config/queue.php';

        $this->connection = new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost']
        );

        $this->channel = $this->connection->channel();
        $this->channel->queue_declare('voltage_data_queue', false, true, false, false);
    }

    public function sendMessage($message) {
        $msg = new AMQPMessage($message, ['delivery_mode' => 2]);
        $this->channel->basic_publish($msg, '', 'voltage_data_queue');
    }

    public function close() {
        $this->channel->close();
        $this->connection->close();
    }
}