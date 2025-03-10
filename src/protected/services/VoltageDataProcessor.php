<?php

namespace app\services;

use app\services\AuthService;
use app\services\FetchVoltageDataService;
use Meters;
use VoltageData;
use Exception;
use Yii;
use CLogger;

class VoltageDataProcessor {
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function process(string $apiMeterId): void {
        try {
            Yii::log("Processing meter: {$apiMeterId}", CLogger::LEVEL_ERROR);

            // Получение нужного счётчика из БД
            $meter = Meters::model()->findByAttributes(['api_id' => $apiMeterId]);

            // Аутентификация
            $sessionId = $this->authService->authenticate();
            Yii::log("Authenticated with session ID: {$sessionId}", CLogger::LEVEL_ERROR);

            // Получение мгновенных значений
            $fetchVoltageDataService = new FetchvoltageDataService($sessionId);
            $voltageData = $fetchVoltageDataService->fetchVoltageData(
                $apiMeterId,
                'hour',
                date('Y-m-d\TH:i:sP')
            );
            Yii::log("Received " . count($voltageData) . " entries", CLogger::LEVEL_ERROR);

            // Обработка записи полученных данных
            foreach ($voltageData as $entry) {
                if (empty($entry['value']) || empty($entry['timestamp'])) {
                    Yii::log("Пропущена некорректная запись: " . print_r($entry, true), CLogger::LEVEL_WARNING);
                    continue;
                }

                $model = new VoltageData();
                $model->attributes = $entry;
                $model->meter_id = $meter->id;

                if (!$model->save()) {
                    Yii::log('Ошибка сохранения: ' . print_r($model->getErrors(), true), CLogger::LEVEL_ERROR);
                }
            }
        } catch (Exception $e) {
            Yii::log('Error: ' . $e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }
}