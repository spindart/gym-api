<?php

namespace App\Domain\Repository;

interface PersonalRecordRepositoryInterface
{
    public function findRankingByMovementId(int $movementId, int $page = 1, int $limit = 10): array;
    public function countRankingByMovementId(int $movementId): int;
}
