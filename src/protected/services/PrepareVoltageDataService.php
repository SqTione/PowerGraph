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

        // Получаем данные из основной таблицы voltage_data
        $mainData = VoltageData::model()->getChartData($selectedMeter->id, $this->period, $this->startDate, $this->endDate);

        // Получаем данные из архивной таблицы thinned_voltage_data
        $archivedData = ThinnedVoltageData::model()->getChartData($selectedMeter->id, $this->period, $this->startDate, $this->endDate);

        // Объединяем данные
        $combinedLabels = array_unique(array_merge($mainData['labels'], $archivedData['labels']));
        sort($combinedLabels); // Сортируем метки по времени

        $combinedData = [
            'labels' => $combinedLabels,
            'dataA' => [],
            'dataB' => [],
            'dataC' => [],
        ];

        // Объединяем данные для каждой фазы
        foreach ($combinedLabels as $label) {
            $combinedData['dataA'][$label] = $mainData['dataA'][$label] ?? $archivedData['dataA'][$label] ?? null;
            $combinedData['dataB'][$label] = $mainData['dataB'][$label] ?? $archivedData['dataB'][$label] ?? null;
            $combinedData['dataC'][$label] = $mainData['dataC'][$label] ?? $archivedData['dataC'][$label] ?? null;
        }

        return [
            'meters' => $meters,
            'selectedMeter' => $selectedMeter,
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'chartData' => $combinedData, // Объединенные данные для графика
        ];
    }
}