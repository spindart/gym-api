<?php

namespace App\Domain\Repository;

interface PersonalRecordRepositoryInterface
{
    public function findRankingByMovementId(int $movementId): array;
}
