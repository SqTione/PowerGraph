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

        // Выполнение транзакции для переноса данных в архивную таблицу
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $this->moveToArchiveTable($preparedOldData);
            $this->deleteOldData();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            Yii::log("Error during data processing: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application');
        }
    }

    /**
     * Получение устаревших данных
     */
    private function getOldData() {
        $data = $this->thinningPeriod->getOldData();

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    /**
     * Перенос аномалий в архивную таблицу
     * @param mixed $data Аномальные значения
     */
    private function moveToArchiveTable($data) {
        foreach ($data as $row) {
            $model = new ThinnedVoltageData();
            $model->attributes = $row;
            if (!$model->save(false)) {
                Yii::log("Failed to save record: " . print_r($model->getErrors(), true), CLogger::LEVEL_ERROR, 'application');
            } else {
                Yii::log("Record saved successfully: " . print_r($row, true), CLogger::LEVEL_ERROR, 'application');
            }
        }
    }

    /**
     * Удаление устаревших данных(старше одной недели)
     */
    private function deleteOldData() {
        // Вычисляем дату неделю назад
        $oneWeekAgo = date('Y-m-d H:i:s', strtotime("-1 week"));

        // Формируем SQL-запрос для удаления старых данных
        $deletedCount = Yii::app()->db->createCommand()
            ->delete('voltage_data', 'timestamp < :date', [':date' => $oneWeekAgo]);

        // Логируем результат
        if ($deletedCount > 0) {
            Yii::log("Deleted $deletedCount records older than one week from voltage_data table.", CLogger::LEVEL_INFO, 'application');
        } else {
            Yii::log("No old data found to delete.", CLogger::LEVEL_INFO, 'application');
        }
    }
}