<?php

declare(strict_types=1);

namespace App\Controller\Api\Order;

use App\Controller\Common\AbstractController;
use App\Controller\Common\Attribute\Route;
use App\Controller\Common\Enum\HttpMethod;
use App\Dto\SimpleHttpResponseDto;
use App\Service\Order\OrderService;
use App\Service\TranslationService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly TranslationService $translationService,
    ) {
        parent::__construct();
    }

    /**  */
    #[Route(method: HttpMethod::GET, path: '/api/order/{id}')]
    public function getOrderById(Request $request, Response $response, int $id): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        $order = $this->orderService->getOrderDetails($id);

        if ($order === null) {
            $responseDto = new SimpleHttpResponseDto(
                statusCode: 404,
                body: [
                    'error' => $this->translationService->translate('order.not_found'),
                    'id' => $id,
                ]
            );
            $response->getBody()->write(\json_encode($responseDto, JSON_PRETTY_PRINT));
            return $response->withStatus($responseDto->statusCode);
        }

        $responseDto = new SimpleHttpResponseDto(
            statusCode: 200,
            body: $order->toArray(),
        );
        $response->getBody()->write(
            // Using an existing serializer, like JMS would probably be better ;-)
            \json_encode($responseDto, JSON_PRETTY_PRINT)
        );

        return $response->withStatus($responseDto->statusCode);
    }
}
