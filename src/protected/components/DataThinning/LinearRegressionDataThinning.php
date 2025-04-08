<?php

Yii::import('application.components.DataThinning.IDataThinningStrategy');
Yii::import('application.components.LinearRegressionCalculator');

class LinearRegressionDataThinning implements IDataThinningStrategy
{
    /**
     * Выполняет прореживание данных
     * @param array $data Массив прореживаемых значений
     * @return array Прореженные данные с соседними точками
    */
    public function thinData($data): array {
        if (empty($data)) {
            Yii::log("No data provided for thinning.", CLogger::LEVEL_ERROR, 'application');
            return [];
        }

        // Группировка данных для дальнейшей обработки
        $groupedData = $this->groupByPhaseAndDate($data);

        // Инициализация массива с аномалиями
        $anomalies = [];

        // Поиск аномалий
        foreach ($groupedData as $phase => $days) {
            foreach ($days as $day => $points) {
                $calculator = new LinearRegressionCalculator($points);
                $outliers = $calculator->findOutliers();

                // Добавление аномалий в массив
                if (!empty($outliers)) {
                    $anomalies[$phase][$day] = $outliers;
                }
            }
        }

        // Получаем аномалии с соседними значениями
        $enrichedAnomalies = $this->addNeighboringPoints($data, $anomalies);

        // Удаляем дубликаты
        $uniqueAnomalies = $this->removeDuplicates($enrichedAnomalies);

        return $uniqueAnomalies;
    }

    /**
     * Группирует данные по фазе и дате для дальнейшей обработки
     * @param array $data Массив прореживаемых значений
     * @return array Массив сгруппированных данных
    */
    private function groupByPhaseAndDate($data): array {
        $groupedData = [];

        foreach ($data as $row) {
            $date = date('Y-m-d', strtotime($row['timestamp']));
            $phase = $row['phase_type'];
            $groupedData[$phase][$date][] = [$row['timestamp'], $row['value']];
        }

        return $groupedData;
    }

    /**
     * Находит соседние к аномальным значениям точки
     * @param array $data Массив прореживаемых значений
     * @param array $anomalies Массив аномалий
     * @return array Массив аномалий с соседними точками
    */
    private function addNeighboringPoints($data, $anomalies): array {
        $enrichedAnomalies = [];

        // Создаем индекс данных по метке времени для быстрого поиска
        $dataByTimestamp = [];
        foreach ($data as $row) {
            $dataByTimestamp[$row['timestamp']] = $row;
        }

        foreach ($anomalies as $phase => $days) {
            foreach ($days as $day => $outliers) {
                foreach ($outliers as $outlier) {
                    $timestamp = $outlier[0];

                    // Добавляем аномалию
                    if (isset($dataByTimestamp[$timestamp])) {
                        $enrichedAnomalies[] = $dataByTimestamp[$timestamp];
                    }

                    // Добавляем предыдущую точку
                    $previousTimestamp = $this->getPreviousTimestamp($data, $timestamp);
                    if ($previousTimestamp && isset($dataByTimestamp[$previousTimestamp])) {
                        $enrichedAnomalies[] = $dataByTimestamp[$previousTimestamp];
                    }

                    // Добавляем следующую точку
                    $nextTimestamp = $this->getNextTimestamp($data, $timestamp);
                    if ($nextTimestamp && isset($dataByTimestamp[$nextTimestamp])) {
                        $enrichedAnomalies[] = $dataByTimestamp[$nextTimestamp];
                    }
                }
            }
        }

        return $enrichedAnomalies;
    }

    /**
     * Получает временную метку предыдущей точки
     * @param array $data Массив прореживаемых значений
     * @param string $currentTimestamp Текущая временная метка
     * @return string Временная метка предыдущей точки
    */
    private function getPreviousTimestamp($data, $currentTimestamp): ?string {
        $timestamps = array_column($data, 'timestamp');
        $index = array_search($currentTimestamp, $timestamps);
        return $index > 0 ? $timestamps[$index - 1] : null;
    }

    /**
     * Получает временную метку следующей точки
     * @param array $data Массив прореживаемых значений
     * @param string $currentTimestamp Текущая временная метка
     * @return string Временная метка следующей точки
     */
    private function getNextTimestamp($data, $currentTimestamp): ?string {
        $timestamps = array_column($data, 'timestamp');
        $index = array_search($currentTimestamp, $timestamps);
        return $index < count($timestamps) - 1 ? $timestamps[$index + 1] : null;
    }

    /**
     * Ищет индекс точки по временной метке
     * @param array $data Массив прореживаемых значений
     * @param string $timestamp Временная метка точки
     * @return int Индекс элемента (-1, если точка не найдена)
    */
    private function findIndexByTimestamp($data, $timestamp): int {
        foreach ($data as $index => $row) {
            if ($row['timestamp'] == $timestamp) {
                return $index;
            }
        }

        // Если точка не найдена, индекс -1
        return -1;
    }

    /**
     * Удаляет дублирующиеся точки из массива с аномалиями
     * @param array $anomalies Массив с аномалиями
     * @return array Массив аномалий без дубликатов
    */
    private function removeDuplicates($anomalies): array {
        $uniqueAnomalies = [];
        foreach ($anomalies as $point) {
            $key = $point['timestamp'];
            $uniqueAnomalies[$key] = $point;
        }
        return array_values($uniqueAnomalies);
    }
}