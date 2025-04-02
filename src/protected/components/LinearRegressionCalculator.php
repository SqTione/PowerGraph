<?php

class LinearRegressionCalculator {
    private $a;                     // Угловой коэффициент
    private $b;                     // Свободный член

    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * Производит вычисления линейной регрессии
    */
    protected function calculate() {
        // Подсчёт количества точек в наборе данных
        $numberOfPoints = count($this->data);

        // Начало подсчёта суммы значений для X и Y
        $xValuesSum= 0;
        $yValuesSum = 0;

        // Подсчёт сумм значений X и Y
        foreach($this->data as $index => $point) {
            $xValuesSum += $point[0];
            $yValuesSum += $point[1];
        }

        // Расчёт средних значений для X и Y
        $xMean = $xValuesSum / $numberOfPoints;
        $yMean = $yValuesSum / $numberOfPoints;

        // Начало подсчёта общего индекса среднеквадратического отклонения
        $xyFirstMomentSum = 0;

        // Начало подсчёта суммы квадратов для значений X и Y
        $xSumOfSquares = 0;
        $ySumOfSquares = 0;

        foreach($this->data as $index => $point) {
            // Вычисление индекса среднеквадратического отклонения для значений X и Y
            $xFirstMoment = $point[0] - $xMean;
            $yFirstMoment = $point[1] - $yMean;

            // Увеличение общего индекса среднеквадратического отклонения
            $xyFirstMomentSum += $xFirstMoment * $yFirstMoment;

            // Увеличение сумм квадратов для значений X и Y
            $xSumOfSquares += pow($xFirstMoment, 2);
            $ySumOfSquares += pow($yFirstMoment, 2);
        }

        // Вычисление квадратного корня сумм квадратов для X и Y
        $rootXSumOfSquares = sqrt($xSumOfSquares);
        $rootYSumOfSquares = sqrt($ySumOfSquares);

        // Вычисление коэффициента корреляции
        $r = $rootXSumOfSquares != 0 && $rootYSumOfSquares != 0 ? ($xyFirstMomentSum / ($rootXSumOfSquares * $rootYSumOfSquares)) : 0;

        // Вычисление дисперсии из сумм квадратов для X и Y
        $xVariance = $xSumOfSquares / ($numberOfPoints - 1);
        $yVariance = $ySumOfSquares / ($numberOfPoints - 1);

        // Вычисление стандартных отклонений для дисперсий X и Y
        $xStandardDeviation = sqrt($xVariance);
        $yStandardDeviation = sqrt($yVariance);

        // Вычисление углового коэффициента
        $this->a = $xStandardDeviation != 0 && $yStandardDeviation != 0 ? $r * ($xStandardDeviation / $yStandardDeviation) : 0;

        // Вычисление свободного члена
        $this->b = $yMean - ($this->a * $xMean);
    }

    /**
     * Находит и возвращает точки с большим отклонением, чем пороговое значение
     * @return array Массив точек с наибольшим отклонением от линейной регрессии
    */
    public function findOutliers(): array {
        // Инициализация массивов для хранения остатков и соответствующих точек
        $residuals = [];
        $outliers = [];

        // Выбираем предсказанные значения и остатки
        foreach($this->data as $index => $point) {
            // Устанавливаем значения X и Y
            $x = $point[0];
            $y = $point[0];

            // Предсказанное значение для линии регрессии
            $yPredicted = $this->a * $x + $this->b;

            // Вычисление остатка
            $residual = $y - $yPredicted;
            $absoluteResidual = abs($residual);

            // Сохраняем остаток и точку
            $residuals[] = [
                'point' => $point,
                'residual' => $residual,
                'absolute_residual' => $absoluteResidual,
            ];
        }

        // Вычисление среднего значения абсолютных остатков
        $meanAbsoluteResidual = array_sum(array_column($residuals, 'absolute_residual'))  / count($residuals);

        // Вычисление стандартного отклонения от остатков
        $variance = array_sum(array_map(function ($r) use ($meanAbsoluteResidual) {
                return pow($r['absolute_residual'] - $meanAbsoluteResidual, 2);
            }, $residuals)) / count($residuals);
        $standardDevianceResiduals = sqrt($variance);

        // Устанавливаем порог как 2 стандартных отклонения
        $threshold = 2 * $standardDevianceResiduals;

        // Находим аномалии
        foreach ($residuals as $data) {
            if ($data['absolute_residual'] > $threshold) {
                $outliers[] = $data['point'];
            }
        }

        return $outliers;
    }
}