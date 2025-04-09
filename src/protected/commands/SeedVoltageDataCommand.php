<?php

class SeedVoltageDataCommand extends CConsoleCommand {
    public function actionIndex($days = 1) {
        $db = Yii::app()->db;
        $command = $db->createCommand();

        $startDate = strtotime(date('Y-m-d 00:00:00'));
        $totalRecords = 0;

        // Генерируем новый счётчик
        $meterId = rand(100, 999);

        $meterData = [
            'name' => 'Тестовый счётчик ' . $meterId,
            'api_id' => $meterId,
        ];

        // Добавляем его в БД
        $command->insert('meters', $meterData);

        // Получаем ID из БД
        $meterDbId = Yii::app()->db->getLastInsertID();

        if (!$meterDbId) {
            echo "Не удалось получить ID счётчика!\n";
            return;
        }

        // Обрабатываем каждый день
        for ($d = 0; $d < $days; $d++) {
            $date = $startDate - ($d * 86400);

            $rows = [];
            $params = [];

            // Генерируем данные для каждой минуты дня
            foreach (range(0, 1439) as $i) {
                $time = date('Y-m-d H:i:s', $date + ($i * 60));

                foreach (['A', 'B', 'C'] as $j => $phase) {
                    $key = ($i * 3) + $j;
                    $rows[] = "(:meter_id$key, :timestamp$key, :phase_type$key, :value$key)";
                    $params[":meter_id$key"] = $meterDbId;
                    $params[":timestamp$key"] = $time;
                    $params[":phase_type$key"] = $phase;
                    $params[":value$key"] = rand(220, 240) + mt_rand() / mt_getrandmax();
                }
            }

            // Формируем SQL-запрос
            $sql = "INSERT INTO voltage_data (meter_id, timestamp, phase_type, value) VALUES " . implode(", ", $rows);
            $command->setText($sql)->execute($params);

            $totalRecords += count($rows);
        }

        echo "Генерация завершена! Добавлено $totalRecords записей для счётчика ID: $meterDbId.\n";
    }
}