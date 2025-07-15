<?php

class m250227_000002_create_meters_table extends CDbMigration {
    public function up() {
        $this->createTable('meters', [
            'id' => 'pk',
            'name' => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT NULL',
            'api_id' => 'int NOT NULL'
        ]);

        // Индекс для быстрого поиска по api_id
        $this->createIndex('idx_api_id', 'meters', 'api_id');
    }

    public function down() {
        $this->dropTable('meters');
    }
}