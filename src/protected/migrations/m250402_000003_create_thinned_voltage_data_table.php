<?php

class m250402_000003_create_thinned_voltage_data_table extends CDbMigration {
    public function up() {
        $this->createTable('thinned_voltage_data', [
            'id' => 'pk',
            'meter_id' => 'int NOT NULL',               // ID счетчика
            'timestamp' => 'datetime NOT NULL',         // Время измерения
            'phase_type' => 'VARCHAR(10) NOT NULL',     // Тип фазы
            'value' => 'decimal(10, 2) NOT NULL',       // Значение напряжения или мощности
        ]);

        // Внешний ключ
        $this->addForeignKey(
            'fk_voltage_data_meter',    // Название ключа
            'thinned_voltage_data',     // Таблица
            'meter_id',                 // Колонка в thinned_voltage_data
            'meters',                   // Связанная таблица
            'id',                       // Колонка в meters
            'CASCADE',                  // Обновление каскадом
            'CASCADE'                   // Удаление каскадом
        );

        // Индекс для ускорения выборок
        $this->createIndex('idx_meter_time', 'thinned_voltage_data', ['meter_id', 'timestamp']);
    }

    public function down() {
        $this->dropTable('thinned_voltage_data');
    }
}