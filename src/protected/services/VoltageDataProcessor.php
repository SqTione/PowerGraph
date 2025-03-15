<?php

class VoltageDataProcessor {
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    /**
     * Обрабатывает и записывает данные мгновенных значений
    */
    public function process(string $apiMeterId): void {
        try {
            Yii::log("Processing meter: {$apiMeterId}", CLogger::LEVEL_ERROR);

            // Получение нужного счётчика из БД
            $meter = Meters::model()->findByAttributes(['api_id' => $apiMeterId]);

            // Аутентификация
            $sessionId = $this->authService->authenticate();
            Yii::log("Authenticated with session ID: {$sessionId}", CLogger::LEVEL_ERROR);

            // Получение мгновенных значений
            $fetchVoltageDataService = new FetchVoltageDataService($sessionId);
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

                // Проверка дублирующихся записей
                $existingRecord = VoltageData::model()->findByAttributes([
                    'meter_id' => $meter->id,
                    'timestamp' => $entry['timestamp'],
                    'phase_type' => $entry['phase_type']
                ]);

                if ($existingRecord) {
                    Yii::log("Пропущена дублирующая запись: " . print_r($entry, true), CLogger::LEVEL_WARNING);
                    continue;
                }

                $model = new VoltageData();
                $model->attributes = $entry;
                $model->meter_id = $meter->id;

                if (!$model->save()) {
                    throw new Exception('Error saving voltage data: ' . $model->getErrors());
                }
            }
        } catch (Exception $e) {
            throw new Exception('Error:' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}