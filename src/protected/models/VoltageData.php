<?php

class VoltageData extends CActiveRecord {
    /**
     * Возвращает имя связанной таблицы базы данных.
     * @return string имя таблицы
    */
    public function tableName() {
        return 'voltage_data';
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

            // Дополнительные правила
            ['timestamp', 'type', 'type' => 'datetime', 'message' => '{attribute} must be a valid date.'],
        ];
    }

    /**
     * Возвращает метки атрибутов
     * @return array метки атрибутов
    */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'meter_id' => 'Meter ID',
            'timestamp' => 'Timestamp',
            'phase_type' => 'Phase Type',
            'value' => 'Value',
        ];
    }

    /**
     * Возвращает статическую модель указанного класса ActiveRecord.
     * @param string $className имя класса ActiveRecord.
     * @return VoltageData статическая модель класса.
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
}