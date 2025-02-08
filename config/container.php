<?php

namespace App\Config;

use DI\Container;
use PDO;
use App\Domain\Repository\MovementRepositoryInterface;
use App\Domain\Repository\PersonalRecordRepositoryInterface;
use App\Infrastructure\Repository\MySqlMovementRepository;
use App\Infrastructure\Repository\MySqlPersonalRecordRepository;


require_once __DIR__ . '/config.php'; 

return [
    PDO::class => function() {
        try {
            return new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    },
    
    MovementRepositoryInterface::class => function(Container $container) {
        return new MySqlMovementRepository($container->get(PDO::class));
    },
    
    PersonalRecordRepositoryInterface::class => function(Container $container) {
        return new MySqlPersonalRecordRepository($container->get(PDO::class));
    }
];