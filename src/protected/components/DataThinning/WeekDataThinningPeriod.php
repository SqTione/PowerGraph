<?php

Yii::import('application.components.DataThinning.IDataThinningPeriod');


class WeekDataThinningPeriod implements IDataThinningPeriod {
    /**
     * Возвращает данные старше недели
     * @return array Массив данных старше недели
    */
    public function getOldData(): array {
        // Время
        $oneWeekAgo = date('Y-m-d H:i:s', strtotime("-1 week"));
        Yii::log("One week ago: " . $oneWeekAgo, CLogger::LEVEL_WARNING);

        $data = Yii::app()->db->createCommand()
            ->select('*')
            ->from('voltage_data')
            ->where("timestamp < :date", [':date' => $oneWeekAgo])
            ->queryAll();

        if (empty($data)) {
            Yii::log("No data older than one week found.", CLogger::LEVEL_WARNING);
            return [];
        }

        Yii::log("Found " . count($data) . " old records.", CLogger::LEVEL_WARNING);

        return $data;
    }
}