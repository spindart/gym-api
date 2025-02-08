<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\PersonalRecord;
use App\Domain\Repository\PersonalRecordRepositoryInterface;
use PDO;
use DateTime;

class MySqlPersonalRecordRepository implements PersonalRecordRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findRankingByMovementId(int $movementId): array
    {
        $sql = "
            SELECT 
                pr.user_id,
                u.name as user_name,
                MAX(pr.value) as value,
                @rank := @rank + 1 as ranking,
                MAX(pr.date) as record_date
            FROM personal_record pr
            JOIN user u ON u.id = pr.user_id
            CROSS JOIN (SELECT @rank := 0) r
            WHERE pr.movement_id = :movement_id
            GROUP BY pr.user_id, u.name
            ORDER BY value DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['movement_id' => $movementId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $records = [];
        foreach ($results as $result) {
            $record = new PersonalRecord(
                $result['user_id'],
                $result['user_name'],
                $result['value'],
                new DateTime($result['record_date'])
            );
            $record->setPosition((int)$result['ranking']);
            $records[] = $record;
        }

        return $records;
    }
}
