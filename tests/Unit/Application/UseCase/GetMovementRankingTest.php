<?php
namespace Tests\Unit\Application\UseCase;

use App\Application\UseCase\GetMovementRanking;
use App\Domain\Entity\Movement;
use App\Domain\Entity\PersonalRecord;
use App\Domain\Repository\MovementRepositoryInterface;
use App\Domain\Repository\PersonalRecordRepositoryInterface;
use App\Domain\Exception\MovementNotFoundException;
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetMovementRankingTest extends TestCase
{
    private $movementRepository;
    private $personalRecordRepository;
    private $useCase;

    protected function setUp(): void
    {
        $this->movementRepository = Mockery::mock(MovementRepositoryInterface::class);
        $this->personalRecordRepository = Mockery::mock(PersonalRecordRepositoryInterface::class);
        $this->useCase = new GetMovementRanking(
            $this->movementRepository,
            $this->personalRecordRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testGetRankingSuccessfully()
    {
        $movementId = 1;
        $movement = new Movement($movementId, 'Deadlift');
        
        $record1 = new PersonalRecord(1, 'John', 200.0, new DateTime('2021-01-01'));
        $record1->setPosition(1);
        
        $record2 = new PersonalRecord(2, 'Jane', 180.0, new DateTime('2021-01-02'));
        $record2->setPosition(2);

        $this->movementRepository
            ->shouldReceive('findById')
            ->with($movementId)
            ->once()
            ->andReturn($movement);

        $this->personalRecordRepository
            ->shouldReceive('findRankingByMovementId')
            ->with($movementId)
            ->once()
            ->andReturn([$record1, $record2]);

        $result = $this->useCase->execute($movementId);

        $expected = [
            'movement' => 'Deadlift',
            'ranking' => [
                [
                    'position' => 1,
                    'user' => 'John',
                    'value' => 200.0,
                    'date' => '2021-01-01 00:00:00'
                ],
                [
                    'position' => 2,
                    'user' => 'Jane',
                    'value' => 180.0,
                    'date' => '2021-01-02 00:00:00'
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetRankingMovementNotFound()
    {
        $this->expectException(MovementNotFoundException::class);

        $movementId = 999;
        
        $this->movementRepository
            ->shouldReceive('findById')
            ->with($movementId)
            ->once()
            ->andReturn(null);

        $this->useCase->execute($movementId);
    }
}