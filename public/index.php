<?php
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Infrastructure\Http\Controller\MovementRankingController;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');
$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
    $docs = [
        'api' => 'Movement Ranking API',
        'version' => '1.0.0',
        'endpoints' => [
            [
                'path' => '/movements/{id}/ranking',
                'method' => 'GET',
                'description' => 'Obter ranking de movimentos pelo ID do movimento',
                'parameters' => [
                    'path' => [
                        'id' => 'Movement ID (integer)'
                    ],
                    'query' => [
                        'page' => 'Número da página (integer, default: 1)',
                        'limit' => 'Itens por página (integer, default: 10)'
                    ]
                ],
                'example' => '/movements/1/ranking?page=1&limit=10',
                'response' => [
                    'movement' => 'string',
                    'ranking' => [
                        [
                            'position' => 'integer',
                            'user' => 'string',
                            'value' => 'number',
                            'date' => 'datetime'
                        ]
                    ],
                    'pagination' => [
                        'current_page' => 'integer',
                        'per_page' => 'integer',
                        'total_items' => 'integer',
                        'total_pages' => 'integer'
                    ]
                ]
            ]
        ]
    ];
    
    $response->getBody()->write(json_encode($docs, JSON_PRETTY_PRINT));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/movements/{id}/ranking', MovementRankingController::class);

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function (Request $request, Response $response) {
    $error = [
        'error' => [
            'status' => 404,
            'message' => 'Rota não encontrada.',
            'details' => 'A URL Solicitada ' . $request->getUri()->getPath() . ' não foi encontrada.'
        ]
    ];
    
    $response->getBody()->write(json_encode($error, JSON_PRETTY_PRINT));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

$app->run();