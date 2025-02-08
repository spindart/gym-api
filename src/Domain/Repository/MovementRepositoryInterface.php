<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Movement;

interface MovementRepositoryInterface
{
    public function findById(int $id): ?Movement;
}