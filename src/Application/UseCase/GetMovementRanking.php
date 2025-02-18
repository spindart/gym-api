<?php

namespace App\Application\UseCase;

use App\Domain\Cache\CacheInterface;
use App\Domain\Repository\MovementRepositoryInterface;
use App\Domain\Repository\PersonalRecordRepositoryInterface;
use App\Domain\Exception\MovementNotFoundException;

class GetMovementRanking
{
    private MovementRepositoryInterface $movementRepository;
    private PersonalRecordRepositoryInterface $personalRecordRepository;
    private CacheInterface $cache;

    public function __construct(
        MovementRepositoryInterface $movementRepository,
        PersonalRecordRepositoryInterface $personalRecordRepository,
        CacheInterface $cache
    ) {
        $this->movementRepository = $movementRepository;
        $this->personalRecordRepository = $personalRecordRepository;
        $this->cache = $cache;
    }

    public function execute(
        int $movementId,
        int $page = 1,
        int $limit = 10,
        bool $onlyBest = true
    ): array {
        $cacheKey = "ranking:{$movementId}:page:{$page}:limit:{$limit}:onlyBest:{$onlyBest}";

        $cachedResult = $this->cache->get($cacheKey);
        if ($cachedResult !== null) {
            return json_decode($cachedResult, true);
        }

        $movement = $this->movementRepository->findById($movementId);
        if (!$movement) {
            throw new MovementNotFoundException();
        }

        $ranking = $this->personalRecordRepository->findRankingByMovementId($movementId, $page, $limit, $onlyBest);
        $total = $this->personalRecordRepository->countRankingByMovementId($movementId, $onlyBest);

        $result = [
            'movement' => $movement->getName(),
            'ranking' => array_map(function ($record) {
                $data = $record->toArray();
                $data['user_id'] = $record->getUserId();
                return $data;
            }, $ranking),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_items' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ];

        $this->cache->set($cacheKey, json_encode($result), 3600);

        return $result;
    }
}
