<?php

namespace App\Application\UseCase;

use App\Domain\Repository\MovementRepositoryInterface;
use App\Domain\Repository\PersonalRecordRepositoryInterface;
use App\Domain\Exception\MovementNotFoundException;

class GetMovementRanking
{
    private MovementRepositoryInterface $movementRepository;
    private PersonalRecordRepositoryInterface $personalRecordRepository;

    public function __construct(
        MovementRepositoryInterface $movementRepository,
        PersonalRecordRepositoryInterface $personalRecordRepository
    ) {
        $this->movementRepository = $movementRepository;
        $this->personalRecordRepository = $personalRecordRepository;
    }

    public function execute(int $movementId): array
    {
        $movement = $this->movementRepository->findById($movementId);
        if (!$movement) {
            throw new MovementNotFoundException();
        }

        $ranking = $this->personalRecordRepository->findRankingByMovementId($movementId);

        return [
            'movement' => $movement->getName(),
            'ranking' => array_map(fn($record) => $record->toArray(), $ranking)
        ];
    }
}
