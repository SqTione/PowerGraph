<?php

class ThinnedVoltageData extends CActiveRecord {
    /**
     * Возвращает имя связанной таблицы базы данных.
     * @return string имя таблицы
     */
    public function tableName() {
        return 'thinned_voltage_data';
    }

    /**
     * Правила валидации атрибутов модели
     * @return array правила валидации
     */
    public function rules() {
        return [
            // Обязательные поля
            ['meter_id, timestamp, phase_type, value', 'required'],

            // Типы данных
            ['meter_id', 'numerical', 'integerOnly' => true],
            ['value', 'numerical'],
            ['phase_type', 'length', 'max' => 10],
            ['phase_type', 'in', 'range' => ['A', 'B', 'C']],
            ['timestamp', 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
        ];
    }

    /**
     * Возвращает метки атрибутов
     * @return array метки атрибутов
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'meter_id' => 'Счётчик',
            'timestamp' => 'Время',
            'phase_type' => 'Фаза',
            'value' => 'Значение',
        ];
    }

    /**
     * Возвращает статическую модель указанного класса ActiveRecord.
     * @param string $className имя класса ActiveRecord.
     * @return ThinnedVoltageData статическая модель класса.
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function relations() {
        return [
            'meter' => [self::BELONGS_TO, 'Meter', 'meter_id'],
        ];
    }

    public static function getChartData(int $meterId, string $period = null, string $startDate = null, string $endDate = null) {
        $criteria = new CDbCriteria();
        $criteria->compare('meter_id', $meterId);
        $criteria->order = 'timestamp ASC'; // Сортируем данные по времени

        // Формируем условия выборки данных в зависимости от периода
        switch ($period) {
            case 'today':
                $criteria->addBetweenCondition('timestamp', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'));
                break;
            case 'yesterday':
                $criteria->addBetweenCondition('timestamp', date('Y-m-d 00:00:00', strtotime('-1 day')), date('Y-m-d 23:59:59', strtotime('-1 day')));
                break;
            case 'week':
                $startDateWeek = date('Y-m-d 00:00:00', strtotime('last Monday'));
                $endDateWeek = date('Y-m-d 23:59:59');
                $criteria->addBetweenCondition('timestamp', $startDateWeek, $endDateWeek);
                break;
            case 'month':
                $criteria->addBetweenCondition('timestamp',
                    date('Y-m-01 00:00:00'),
                    date('Y-m-t 23:59:59')
                );
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $criteria->addBetweenCondition('timestamp',
                        date('Y-m-d 00:00:00', strtotime($startDate)),
                        date('Y-m-d 23:59:59', strtotime($endDate))
                    );
                }
                break;
            default:
                $criteria->addBetweenCondition('timestamp', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'));
        }

        // Выполняем запрос к базе данных
        $data = self::model()->findAll($criteria);

        // Группируем данные
        $result = [
            'labels' => [],
            'dataA' => [],
            'dataB' => [],
            'dataC' => [],
        ];

        foreach ($data as $record) {
            // Форматируем временную метку
            $timestamp = Yii::app()->dateFormatter->format('d.MM.yyyy HH:mm:ss', $record->timestamp);

            // Если метка еще не добавлена, добавляем её
            if (!isset($result['labels'][$timestamp])) {
                $result['labels'][$timestamp] = true;
            }

            // Заполняем данные для каждой фазы
            switch ($record->phase_type) {
                case 'A':
                    $result['dataA'][$timestamp] = $record->value;
                    break;
                case 'B':
                    $result['dataB'][$timestamp] = $record->value;
                    break;
                case 'C':
                    $result['dataC'][$timestamp] = $record->value;
                    break;
            }
        }

        // Преобразуем labels обратно в массив
        $result['labels'] = array_keys($result['labels']);

        return $result;
    }
}