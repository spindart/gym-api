<?php
use Phinx\Seed\AbstractSeed;

class InitialSeeder extends AbstractSeed
{
    public function run(): void
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');

        $this->table('personal_record')->truncate();
        $this->table('movement')->truncate();
        $this->table('user')->truncate();

        $this->execute('SET FOREIGN_KEY_CHECKS=1;');

        $users = [
            ['id' => 1, 'name' => 'Joao'],
            ['id' => 2, 'name' => 'Jose'],
            ['id' => 3, 'name' => 'Paulo']
        ];
        $this->table('user')->insert($users)->save();

        $movements = [
            ['id' => 1, 'name' => 'Deadlift'],
            ['id' => 2, 'name' => 'Back Squat'],
            ['id' => 3, 'name' => 'Bench Press']
        ];
        $this->table('movement')->insert($movements)->save();

        $records = [
            ['user_id' => 1, 'movement_id' => 1, 'value' => 100.0, 'date' => '2021-01-01 00:00:00'],
            ['user_id' => 2, 'movement_id' => 1, 'value' => 180.0, 'date' => '2021-01-02 00:00:00'],
            ['user_id' => 3, 'movement_id' => 1, 'value' => 150.0, 'date' => '2021-01-03 00:00:00'],
            ['user_id' => 1, 'movement_id' => 1, 'value' => 110.0, 'date' => '2021-01-04 00:00:00'],
            ['user_id' => 2, 'movement_id' => 1, 'value' => 110.0, 'date' => '2021-01-04 00:00:00'],
            ['user_id' => 2, 'movement_id' => 1, 'value' => 140.0, 'date' => '2021-01-05 00:00:00'],
            ['user_id' => 2, 'movement_id' => 1, 'value' => 190.0, 'date' => '2021-01-06 00:00:00'],
            ['user_id' => 3, 'movement_id' => 1, 'value' => 170.0, 'date' => '2021-01-01 00:00:00'],
            ['user_id' => 3, 'movement_id' => 1, 'value' => 120.0, 'date' => '2021-01-02 00:00:00'],
            ['user_id' => 3, 'movement_id' => 1, 'value' => 130.0, 'date' => '2021-01-03 00:00:00'],
            ['user_id' => 1, 'movement_id' => 2, 'value' => 130.0, 'date' => '2021-01-03 00:00:00'],
            ['user_id' => 2, 'movement_id' => 2, 'value' => 130.0, 'date' => '2021-01-03 00:00:00'],
            ['user_id' => 3, 'movement_id' => 2, 'value' => 125.0, 'date' => '2021-01-03 00:00:00'],
            ['user_id' => 1, 'movement_id' => 2, 'value' => 110.0, 'date' => '2021-01-05 00:00:00'],
            ['user_id' => 1, 'movement_id' => 2, 'value' => 100.0, 'date' => '2021-01-01 00:00:00'],
            ['user_id' => 2, 'movement_id' => 2, 'value' => 120.0, 'date' => '2021-01-01 00:00:00'],
            ['user_id' => 3, 'movement_id' => 2, 'value' => 120.0, 'date' => '2021-01-01 00:00:00'],
        ];
        $this->table('personal_record')->insert($records)->save();
    }
}
