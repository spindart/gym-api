<?php

namespace App\Infrastructure\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

class ErrorHandler extends SlimErrorHandler
{
    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;
        $statusCode = 500;
        $error = [
            'error' => [
                'status' => $statusCode,
                'message' => 'Internal Server Error',
                'details' => $this->displayErrorDetails ? $exception->getMessage() : 'An internal error has occurred.'
            ]
        ];

        if ($exception instanceof HttpNotFoundException) {
            $statusCode = 404;
            $error['error']['status'] = 404;
            $error['error']['message'] = 'Route not found';
        }

        $payload = json_encode($error, JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}
