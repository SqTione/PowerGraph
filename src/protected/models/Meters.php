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
            ['name, api_id', 'length', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID счётчика',
            'name' => 'Название счётчика',
            'api_id' => 'API ID счётчика'
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
}