<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

Yii::import('application.components.Queue.QueueProducerInterface');

class RabbitMQProducer implements QueueProducerInterface {
    private $connection;
    private $channel;
    private $config;

    public function __construct(array $config) {
        $this->config = $config;
        $this->connect();
    }

    /**
     * Подключение к RabbitMQ с данными из конфига
    */
    private function connect() {
        $this->connection = new AMQPStreamConnection(
            $this->config['host'],
            $this->config['port'],
            $this->config['user'],
            $this->config['password'],
            $this->config['vhost']
        );

        $this->channel = $this->connection->channel();
        $this->channel->queue_declare('voltage_data_queue', false, true, false, false);
    }

    /**
     * Отправка сообщения в очередь.
    */
    public function sendMessage(string $message): void {
        $msg = new AMQPMessage($message, ['delivery_mode' => 2]);
        $this->channel->basic_publish($msg, '', 'voltage_data_queue');
    }

    /**
     * Закрытие соединения с RabbitMQ
    */
    public function close(): void {
        try {
            if (isset($this->channel)) {
                $this->channel->close();
                $this->channel = null;
            }
            if (isset($this->connection)) {
                $this->connection->close();
                $this->connection = null;
            }
        } catch (\Exception $e) {
            Yii::log('Close error: ' . $e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }
}