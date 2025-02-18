<?php

namespace Tests\Unit\Application\UseCase;

use App\Application\UseCase\GetMovementRanking;
use App\Domain\Entity\Movement;
use App\Domain\Entity\PersonalRecord;
use App\Domain\Repository\MovementRepositoryInterface;
use App\Domain\Repository\PersonalRecordRepositoryInterface;
use App\Domain\Exception\MovementNotFoundException;
use App\Domain\Cache\CacheInterface;
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetMovementRankingTest extends TestCase
{
    /** @var MovementRepositoryInterface|Mockery\MockInterface */
    private $movementRepository;

    /** @var PersonalRecordRepositoryInterface|Mockery\MockInterface */
    private $personalRecordRepository;

    /** @var CacheInterface|Mockery\MockInterface */
    private $cache;

    private GetMovementRanking $useCase;

    protected function setUp(): void
    {
        $this->movementRepository = Mockery::mock(MovementRepositoryInterface::class);
        $this->personalRecordRepository = Mockery::mock(PersonalRecordRepositoryInterface::class);
        $this->cache = Mockery::mock(CacheInterface::class);

        $this->useCase = new GetMovementRanking(
            $this->movementRepository,
            $this->personalRecordRepository,
            $this->cache
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testGetRankingSuccessfully()
    {
        $movementId = 1;
        $page = 1;
        $limit = 10;
        $cacheKey = "ranking:{$movementId}:page:{$page}:limit:{$limit}";
        $this->cache
            ->shouldReceive('get')
            ->with($cacheKey)
            ->once()
            ->andReturn(null);
        $this->cache
            ->shouldReceive('set')
            ->with($cacheKey, Mockery::any(), 3600)
            ->once();
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
            ->with($movementId, $page, $limit)
            ->once()
            ->andReturn([$record1, $record2]);
        $this->personalRecordRepository
            ->shouldReceive('countRankingByMovementId')
            ->with($movementId)
            ->once()
            ->andReturn(2);
        $result = $this->useCase->execute($movementId, $page, $limit);
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
            ],
            'pagination' => [
                'current_page' => 1,
                'per_page' => 10,
                'total_items' => 2,
                'total_pages' => 1
            ]
        ];
        $this->assertEquals($expected, $result);
    }

    public function testGetRankingFromCache()
    {
        $cachedData = [
            'movement' => 'Deadlift',
            'ranking' => [],
            'pagination' => [
                'current_page' => 1,
                'per_page' => 10,
                'total_items' => 0,
                'total_pages' => 0
            ]
        ];
        $this->cache
            ->shouldReceive('get')
            ->once()
            ->with("ranking:1:page:1:limit:10")
            ->andReturn(json_encode($cachedData));
        $this->movementRepository->shouldNotReceive('findById');
        $this->personalRecordRepository->shouldNotReceive('findRankingByMovementId');
        $this->personalRecordRepository->shouldNotReceive('countRankingByMovementId');
        $this->cache->shouldNotReceive('set');
        $result = $this->useCase->execute(1);
        $this->assertEquals($cachedData, $result);
    }

    public function testExecuteWithPagination()
    {
        $movementId = 1;
        $page = 2;
        $limit = 2;
        $cacheKey = "ranking:{$movementId}:page:{$page}:limit:{$limit}";
        $this->cache
            ->shouldReceive('get')
            ->with($cacheKey)
            ->once()
            ->andReturn(null);
        $this->cache
            ->shouldReceive('set')
            ->with($cacheKey, Mockery::any(), 3600)
            ->once();
        $movement = new Movement($movementId, 'Deadlift');
        $record1 = new PersonalRecord(1, 'User1', 100.0, new DateTime());
        $record1->setPosition(3);
        $record2 = new PersonalRecord(2, 'User2', 90.0, new DateTime());
        $record2->setPosition(4);
        $records = [$record1, $record2];
        $this->movementRepository
            ->shouldReceive('findById')
            ->with($movementId)
            ->once()
            ->andReturn($movement);
        $this->personalRecordRepository
            ->shouldReceive('findRankingByMovementId')
            ->with($movementId, $page, $limit)
            ->once()
            ->andReturn($records);
        $this->personalRecordRepository
            ->shouldReceive('countRankingByMovementId')
            ->with($movementId)
            ->once()
            ->andReturn(5);
        $result = $this->useCase->execute($movementId, $page, $limit);
        $this->assertEquals('Deadlift', $result['movement']);
        $this->assertCount(2, $result['ranking']);
        $this->assertEquals([
            'current_page' => 2,
            'per_page' => 2,
            'total_items' => 5,
            'total_pages' => 3
        ], $result['pagination']);
    }

    public function testExecuteThrowsExceptionWhenMovementNotFound()
    {
        $this->cache->shouldReceive('get')->once()->andReturn(null);
        $this->movementRepository
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);
        $this->expectException(MovementNotFoundException::class);
        $this->useCase->execute(1);
    }
}
