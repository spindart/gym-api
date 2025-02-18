<?php

// src/Infrastructure/Http/Controller/MovementRankingController.php
namespace App\Infrastructure\Http\Controller;

use App\Application\UseCase\GetMovementRanking;
use App\Domain\Exception\MovementNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Movement Ranking API",
 *     version="1.0.0"
 * )
 */
class MovementRankingController
{
    private GetMovementRanking $useCase;

    public function __construct(GetMovementRanking $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * @OA\Get(
     *     path="/movements/{id}/ranking",
     *     summary="Retorna o ranking de um movimento",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do movimento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Número da página",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Itens por página",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ranking do movimento",
     *         @OA\JsonContent(
     *             @OA\Property(property="movement", type="string"),
     *             @OA\Property(property="ranking", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="position", type="integer"),
     *                     @OA\Property(property="user", type="string"),
     *                     @OA\Property(property="value", type="number"),
     *                     @OA\Property(property="date", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total_items", type="integer"),
     *                 @OA\Property(property="total_pages", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movimento não encontrado"
     *     )
     * )
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $queryParams = $request->getQueryParams();
        $page = isset($queryParams['page']) ? (int) $queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : 10;

        try {
            $result = $this->useCase->execute($id, $page, $limit);
            $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (MovementNotFoundException $e) {
            $response->getBody()->write(json_encode(['error' => 'Movement not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    }
}
