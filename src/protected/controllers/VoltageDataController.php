<?php

class VoltageDataController extends Controller {
    public function actionIndex() {
        // Получаем данные из GET параметров
        $meterId = Yii::app()->request->getQuery('meter_id');
        $period = Yii::app()->request->getQuery('period', 'today');
        $startDate = Yii::app()->request->getQuery('start_date');
        $endDate = Yii::app()->request->getQuery('end_date');

        // Получаем подготовленные данные
        $service = new PrepareVoltageDataService($meterId, $period, $startDate, $endDate);
        $data = $service->prepareVoltageData();

        // Если это AJAX-запрос, возвращаем только данные
        if(Yii::app()->request->isAjaxRequest) {
            echo CJSON::encode($data['chartData']);
            Yii::app()->end();
        }

        // Передаем данные в представление
        $this->render('voltageChart', $data);
    }
}