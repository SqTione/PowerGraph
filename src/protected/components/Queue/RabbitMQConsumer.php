<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;

Yii::import('application.components.Queue.QueueConsumerInterface');

class RabbitMQConsumer implements QueueConsumerInterface {
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
     * Начинает приём сообщений из очереди
    */
    public function consume(callable $callback): void {
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume('voltage_data_queue', '', false, false, false, false, $callback);
    }

    /**
     * Запускает цикл ожидания
    */
    public function wait(): void {
        echo 'Waiting for messages...' . PHP_EOL;
        $this->channel->wait(null, false, 5);
    }

    /**
     * Закрытие соединения с RabbitMQ
     */
    public function close(): void {
        $this->channel->close();
        $this->connection->close();
    }
}