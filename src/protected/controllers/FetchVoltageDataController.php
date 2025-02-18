<?php
class FetchVoltageDataController extends CController {
    private $fetchVoltageDataService;

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);

        // Получаем ID сессии
        $sessionId = Yii::app()->session['sessionId'];
        if (empty($sessionId)) {
            throw new RuntimeException('Session ID is not set. Please authenticate first.');
        }

        // Инициализация сервиса получения данных с API
        $this->fetchVoltageDataService = new FetchVoltageDataService($sessionId);
    }

    /**
     * Метод для получения мгновенных значений с API
     */
    public function actionFetchVoltageData() {
        $meterId = Yii::app()->request->getParam('meterId');
        $periodType = Yii::app()->request->getParam('periodType');
        $periodValue = Yii::app()->request->getParam('periodValue');
        $limit = Yii::app()->request->getParam('limit', 1000);

        try {
            $voltageData = $this->fetchVoltageDataService->fetchVoltageData($meterId, $periodType, $periodValue, $limit);
            echo CJSON::encode(['data' => $voltageData]);
        } catch (InvalidArgumentException $e) {
            echo CJSON::encode(['error' => $e->getMessage()]);
        } catch (RuntimeException $e) {
            echo CJSON::encode(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            echo CJSON::encode(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }
}