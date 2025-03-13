<?php

use CJSON;
use VoltageData;
use Yii;
use Meters;
use CHttpException;

class VoltageDataController extends Controller {
    public function actionIndex() {
        // Получаем данные из GET параметров
        $meterId = Yii::app()->request->getQuery('meter_id');
        $period = Yii::app()->request->getQuery('period', 'today');
        $startDate = Yii::app()->request->getQuery('start_date');
        $endDate = Yii::app()->request->getQuery('end_date');

        if (!$meterId) {
            throw new CHttpException(400, 'Не указан meter_id');
        }

        // Получаем список счётчиков
        $meters = Meters::model()->findAll();
        $selectedMeter = Meters::model()->getValidatedMeter($meterId);

        // Получаем данные для графика
        $chartData = VoltageData::model()->getChartData($selectedMeter->id, $period, $startDate, $endDate);

        // Если это AJAX-запрос, возвращаем только данные
        if(Yii::app()->request->isAjaxRequest) {
            echo CJSON::encode($chartData);
            Yii::app()->end();
        }

        // Передаем данные в представление
        $this->render('voltageChart', [
            'meters' => $meters,
            'selectedMeter' => $selectedMeter,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'chartData' => $chartData,
        ]);
    }
}