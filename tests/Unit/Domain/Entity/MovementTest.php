<?php

namespace Tests\Unit\Domain\Entity;

use App\Domain\Entity\Movement;
use PHPUnit\Framework\TestCase;

class MovementTest extends TestCase
{
    public function testCreateMovement()
    {
        $movement = new Movement(1, 'Deadlift');
        $this->assertEquals(1, $movement->getId());
        $this->assertEquals('Deadlift', $movement->getName());
    }
}
