<?php

class m250404_000004_add_decription_to_meters extends CDbMigration {
    public function up() {
        $this->addColumn('meters', 'description', 'VARCHAR(255) DEFAULT "Вы можете добавить описание счётчика" NULL');
    }

    public function down() {
        $this->dropColumn('meters', 'description');
    }
}