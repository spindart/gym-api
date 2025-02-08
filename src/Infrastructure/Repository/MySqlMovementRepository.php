<?php

namespace App\Infrastructure\Repository;

use PDO;
use App\Domain\Repository\MovementRepositoryInterface;
use App\Domain\Entity\Movement;

class MySqlMovementRepository implements MovementRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Movement
    {
        $stmt = $this->pdo->prepare("
            SELECT id, name 
            FROM movement 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }

        return new Movement($result['id'], $result['name']);
    }
}