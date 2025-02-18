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

    public function findRankingByMovementId(int $movementId, int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;

        $sql = "
            SELECT 
                pr.user_id,
                u.name as user_name,
                MAX(pr.value) as value,
                @rank := @rank + 1 as ranking,
                MAX(pr.date) as record_date
            FROM personal_record pr
            JOIN user u ON u.id = pr.user_id
            CROSS JOIN (SELECT @rank := :offset) r
            WHERE pr.movement_id = :movement_id
            GROUP BY pr.user_id, u.name
            ORDER BY value DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':movement_id', $movementId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->hydrateRecords($results);
    }

    public function countRankingByMovementId(int $movementId): int
    {
        $sql = "
            SELECT COUNT(DISTINCT user_id) as total
            FROM personal_record
            WHERE movement_id = :movement_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['movement_id' => $movementId]);

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function hydrateRecords(array $results): array
    {
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
