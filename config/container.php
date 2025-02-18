<?php

namespace App\Config;

use DI\Container;
use PDO;
use Predis\Client;
use App\Domain\Cache\CacheInterface;
use App\Infrastructure\Cache\FallbackCache;
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
    
    Client::class => function() {
        $isDocker = getenv('DOCKER') === 'true';
        return new Client([
            'scheme' => 'tcp',
            'host'   => $isDocker ? 'redis' : '127.0.0.1',
            'port'   => 6379,
            'read_write_timeout' => 0,
        ]);
    },

    CacheInterface::class => function(Container $container) {
        return new FallbackCache($container->get(Client::class));
    },
    
    MovementRepositoryInterface::class => function(Container $container) {
        return new MySqlMovementRepository($container->get(PDO::class));
    },
    
    PersonalRecordRepositoryInterface::class => function(Container $container) {
        return new MySqlPersonalRecordRepository($container->get(PDO::class));
    }
];