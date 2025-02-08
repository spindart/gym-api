<?php

use Phinx\Migration\AbstractMigration;

class CreateInitialTables extends AbstractMigration
{
    public function change()
    {
        $this->table('user')
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->create();

        $this->table('movement')
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->create();

        $this->table('personal_record')
            ->addColumn('user_id', 'integer')
            ->addColumn('movement_id', 'integer', ['null' => false])
            ->addColumn('value', 'float', ['null' => false])
            ->addColumn('date', 'datetime', ['null' => false])
            ->create();

        $this->execute('ALTER TABLE personal_record MODIFY user_id INT UNSIGNED');
        $this->execute('ALTER TABLE personal_record MODIFY movement_id INT UNSIGNED');

        $this->table('personal_record')
            ->addForeignKey('user_id', 'user', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->addForeignKey('movement_id', 'movement', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->update();
    }
}
