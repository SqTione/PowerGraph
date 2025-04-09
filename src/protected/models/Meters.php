<?php

class Meters extends CActiveRecord {
    public function tableName() {
        return 'meters';
    }

    public function rules() {
        return [
            // Обязательные поля
            ['name, api_id', 'required'],

            // Правила валидации
            ['api_id', 'numerical', 'integerOnly' => true],
            ['api_id', 'unique'],
            ['name, api_id, description', 'length', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID счётчика',
            'name' => 'Название счётчика',
            'api_id' => 'API ID счётчика',
            'description' => 'Описание счётчика'
        ];
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function relations() {
        return [
            'voltageData' => [self::HAS_MANY, 'VoltageData', 'meter_id'],
        ];
    }

    /**
     * Возвращает счётчик по API ID
     * @param int $apiId
     * @return Meters|null
    */
    public static function findByApiId(int $apiId): Meters {
        return self::model()->findByAttributes(['api_id' => $apiId]);
    }

    /**
     * Возвращает счётчик c проверкой существования
     * @param int $id
     * @throws CHttpException Счётчик с данным $id не найден
     * @return Meters
    */
    public static function getValidatedMeter(int $id): Meters {
        if (!$meter = self::model()->findByPk($id)) {
            throw new CHttpException(404, "Meter with id {$id} not found");
        }

        return $meter;
    }
}