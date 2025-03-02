<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;

class QueueConsumerCommand extends CConsoleCommand {
    private $shouldStop = false;

    public function actionIndex() {
        Yii::import('application.models.Meters');
        Yii::import('application.models.VoltageData');
        Yii::import('application.services.AuthService');
        Yii::import('application.services.FetchVoltageDataService');

        $config = require Yii::getPathOfAlias('application.config.queue').'.php';
        $connection = new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost']
        );
        $channel = $connection->channel();
        $channel->queue_declare('voltage_data_queue', false, true, false, false);

        // Обработка сигналов для graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'stopConsuming']);
            pcntl_signal(SIGINT, [$this, 'stopConsuming']);
        }

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {
            try {
                $data = json_decode($msg->body, true);
                $apiMeterId = $data['meter_id'];

                // Находим meter_id для api_meter_id
                $meter = Meters::model()->findByAttributes(['api_id' => $apiMeterId]);

                $meterId = $meter->id;

                // Получение session_id через аутентификацию
                $auth = new AuthService();
                $sessionId = $auth->authenticate('demo@demo.demo', 'demo');

                $service = new FetchVoltageDataService($sessionId);
                $voltageData = $service->fetchVoltageData(
                    $apiMeterId,
                    'hour',
                    date('Y-m-d\TH:i:sP'),
                    60
                );

                foreach ($voltageData as $entry) {
                    // Пропускаем записи с пустыми значениями
                    if (empty($entry['value']) || empty($entry['timestamp'])) {
                        Yii::log("Пропущена некорректная запись: " . print_r($entry, true), CLogger::LEVEL_WARNING);
                        continue;
                    }

                    $model = new VoltageData();
                    $model->attributes = $entry;
                    $model->meter_id = $meterId;
                    if (!$model->save()) {
                        Yii::log('Ошибка сохранения: ' . print_r($model->getErrors(), true), CLogger::LEVEL_ERROR);
                    }
                }
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            }
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('voltage_data_queue', '', false, false, false, false, $callback);

        // Основной цикл обработки сообщений
        while (!$this->shouldStop) {
            try {
                $channel->wait(null, false, 5);
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }
            } catch (Exception $e) {
                Yii::log('Connection closed: ' . $e->getMessage(), CLogger::LEVEL_ERROR);
                break;
            } catch (Exception $e) {
                Yii::log('Processing error: ' . $e->getMessage(), CLogger::LEVEL_ERROR);
                break;
            }
        }

        $channel->close();
        $connection->close();
    }

    public function stopConsuming() {
        $this->shouldStop = true;
    }

    public function actionScheduleAllMeters() {
        Yii::import('application.models.Meters');
        Yii::import('application.components.QueueProducer');

        $meters = Meters::model()->findAll();
        foreach ($meters as $meter) {
            $producer = new QueueProducer();
            $producer->sendMessage(json_encode(['meter_id' => $meter->api_id]));
            $producer->close();
        }
    }
}