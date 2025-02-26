<?php

class m250222_000001_create_voltage_data_table extends CDbMigration {
    public function up() {
        $this->createTable('voltage_data', [
            'id' => 'pk',
            'meter_id' => 'int NOT NULL',               // ID счетчика
            'timestamp' => 'datetime NOT NULL',         // Время измерения
            'phase_type' => 'VARCHAR(10) NOT NULL',     // Тип фазы
            'value' => 'decimal(10, 2) NOT NULL',       // Значение напряжения или мощности
        ]);

        // Индекс для ускорения поиска по meter_id, timestamp и phase_type
        $this->createIndex('idx_meter_id_timestamp_phase', 'voltage_data', ['meter_id', 'timestamp', 'phase_type']);
    }

    public function down() {
        $this->dropTable('voltage_data');
    }
}
