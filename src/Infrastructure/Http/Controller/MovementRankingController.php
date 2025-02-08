<?php

// src/Infrastructure/Http/Controller/MovementRankingController.php
namespace App\Infrastructure\Http\Controller;

use App\Application\UseCase\GetMovementRanking;
use App\Domain\Exception\MovementNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Tecnofit API",
 *         description="API para gerenciamento de recordes pessoais de movimentos"
 *     )
 * )
 */
class MovementRankingController
{
    private GetMovementRanking $getMovementRanking;

    public function __construct(GetMovementRanking $getMovementRanking)
    {
        $this->getMovementRanking = $getMovementRanking;
    }

        /**
     * @OA\Get(
     *     path="/movements/{id}/ranking",
     *     summary="Retorna o ranking de um movimento específico",
     *     tags={"Movements"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do movimento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ranking do movimento",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="movement", type="string", example="Deadlift"),
     *             @OA\Property(
     *                 property="ranking",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="position", type="integer", example=1),
     *                     @OA\Property(property="user", type="string", example="Jose"),
     *                     @OA\Property(property="value", type="number", format="float", example=190.0),
     *                     @OA\Property(property="date", type="string", format="datetime", example="2021-01-06 00:00:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movimento não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Movement not found")
     *         )
     *     )
     * )
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $result = $this->getMovementRanking->execute((int)$args['id']);
            $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (MovementNotFoundException $e) {
            $response->getBody()->write(json_encode(['error' => 'Movement not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    }
}