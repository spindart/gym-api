<?php
namespace Tests\Unit\Domain\Entity;

use App\Domain\Entity\PersonalRecord;
use DateTime;
use PHPUnit\Framework\TestCase;

class PersonalRecordTest extends TestCase
{
    public function testCreatePersonalRecord()
    {
        $date = new DateTime('2021-01-01');
        $record = new PersonalRecord(1, 'John', 100.0, $date);
        $record->setPosition(1);
        
        $expected = [
            'position' => 1,
            'user' => 'John',
            'value' => 100.0,
            'date' => '2021-01-01 00:00:00'
        ];
        
        $this->assertEquals($expected, $record->toArray());
    }
}