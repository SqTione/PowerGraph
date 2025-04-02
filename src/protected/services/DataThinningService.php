<?php

Yii::import('application.models.ThinnedVoltageData');

class DataThinningService {
    private $strategy;
    private $thinningPeriod;

    public function __construct($strategy, $thinningPeriod) {
        $this->strategy = $strategy;
        $this->thinningPeriod = $thinningPeriod;
    }

    public function processOldData() {
        $oldData = $this->getOldData();

        if(empty($oldData)) {
            Yii::log("No old data found to process.", CLogger::LEVEL_WARNING, 'application');
            return;
        }

        // Подготавливаем данные для стратегии, удаляя ID
        $preparedOldData = array_map(function($row) {
            return [
                'timestamp' => $row['timestamp'],
                'value' => $row['value'],
                'meter_id' => $row['meter_id'],
                'phase_type' => $row['phase_type'],
            ];
        }, $oldData);

        Yii::log("Prepared data: " . print_r($preparedOldData, true), CLogger::LEVEL_WARNING, 'application');

        // Прореживаем данные
        $thinnedData = $this->strategy->thinData($oldData);

        Yii::log("Thinned data: " . print_r($thinnedData, true), CLogger::LEVEL_WARNING, 'application');

        // Заносим данные в архивную таблицу
        $this->moveToArchiveTable($preparedOldData);

        Yii::log("Data thinning process completed successfully.", CLogger::LEVEL_WARNING, 'application');
    }

    private function getOldData() {
        $data = $this->thinningPeriod->getOldData();

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    private function moveToArchiveTable($data) {
        foreach ($data as $row) {
            $model = new ThinnedVoltageData();
            $model->attributes = $row;
            if (!$model->save(false)) {
                Yii::log("Failed to save record: " . print_r($model->getErrors(), true), CLogger::LEVEL_ERROR, 'application');
            } else {
                Yii::log("Record saved successfully: " . print_r($row, true), CLogger::LEVEL_ERROR, 'application');
            }

            /*Yii::app()->db->createCommand()
                ->insert('thinned_voltage_data', [
                    'meter_id' => $row['meter_id'],
                    'timestamp' => $row['timestamp'],
                    'phase_type' => $row['phase_type'],
                    'value' => $row['value'],
                ]);

            Yii::log("Moved " . count($data) . " records", CLogger::LEVEL_WARNING);*/
        }
    }
}