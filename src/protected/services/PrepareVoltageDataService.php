<?php

class PrepareVoltageDataService {
    private int $meterId;
    private string $period;
    private $startDate;
    private $endDate;

    public function __construct(int $meterId, string $period, $startDate = null, $endDate = null) {
        $this->meterId = $meterId;
        $this->period = $period;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Возвращает массив мгновенных значений
     * @throws CHttpException
     */
    public function prepareVoltageData(): array {
        // Проверка наличия ID счётчика
        if (!$this->meterId) {
            throw new CHttpException(400, 'Не указан meter_id');
        }

        // Получаем список счётчиков
        $meters = Meters::model()->findAll();
        $selectedMeter = Meters::model()->getValidatedMeter($this->meterId);

        // Получаем данные для графика
        $chartData = VoltageData::model()->getChartData($selectedMeter->id, $this->period, $this->startDate, $this->endDate);

        return [
            'meters' => $meters,
            'selectedMeter' => $selectedMeter,
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'chartData' => $chartData,
        ];
    }
}