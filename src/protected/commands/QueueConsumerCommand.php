<?php

// Импорт компонентов(автозагрузчик здесь не работает)
Yii::import('application.components.Queue.RabbitMQConsumer');
Yii::import('application.services.AuthService');
Yii::import('application.services.FetchVoltageDataService');
Yii::import('application.services.VoltageDataProcessor');
Yii::import('application.components.Queue.RabbitMQProducer');
Yii::import('application.components.EnvHelper');
Yii::import('application.models.Meters');
Yii::import('application.models.VoltageData');

class QueueConsumerCommand extends CConsoleCommand {
    private $shouldStop = false;

    /**
     * Получение данных со всех счетчиков и добавление данных в базу.
     * Команда для запуска: ./yiic queueconsumer
    */
    public function actionIndex() {
        $config = require Yii::getPathOfAlias('application.config.queue') . '.php';

        // Получаем данные для аутентификации из .env
        $login = EnvHelper::getEnv('API_LOGIN');
        $password = EnvHelper::getEnv('API_PASSWORD');

        // Инициализируем обработчик сообщений RabbitMQ
        $consumer = new RabbitMQConsumer($config);

        // Инициализируем обработчик данных вольтажа
        $processor = new VoltageDataProcessor(
            new AuthService($login, $password),
        );

        // Обработчик сообщений из очереди
        $callback = function ($msg) use ($processor) {
            $data = json_decode($msg->body, true);
            $processor->process($data['meter_id']);
        };

        // Обработка сообщений
        $consumer->consume($callback);

        // Ожидание сообщений
        while (!$this->shouldStop) {
            try {
                $consumer->wait();
            } catch (Exception $e) {
                Yii::log('Error: ' . $e->getMessage(), CLogger::LEVEL_ERROR);
                break;
            }
        }

        // Закрытие соединения
        try {
            $consumer->close();
        } catch (\Exception $e) {
            Yii::log('Close error: ' . $e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }

    /**
     * Команда для получения данных со всех счётчиков в БД
    */
    public function actionScheduleAllMeters() {
        $config = require Yii::getPathOfAlias('application.config.queue').'.php';
        
        $producer = new RabbitMQProducer($config);

        foreach (Meters::model()->findAll() as $meter) {
            $producer->sendMessage(json_encode(['meter_id' => $meter->api_id]));
        }

        $producer->close();
    }
}