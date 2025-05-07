<?php

declare(strict_types=1);

namespace App\Repository\Order;

use App\Enum\CurrencyEnum;
use App\Enum\Order\OrderStatusEnum;
use App\Model\Order\Order;
use App\Model\Order\OrderItem;
use Decimal\Decimal;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class APIOrderRepository implements OrderRepositoryInterface
{
    protected Client $client;
    protected string $apiUrl;

    public function __construct(Client $client, string $apiUrl)
    {
        $this->client = $client;
        $this->apiUrl = rtrim($apiUrl, '/');
    }

    public function findById(int $id): ?Order
    {
        try {
            $response = $this->client->get("{$this->apiUrl}/orders/{$id}");
            $data = json_decode($response->getBody()->getContents(), true);

            return $this->mapToOrder($data); // TODO: TO DTO
        } catch (RequestException $e) {
            error_log($e->getMessage()); // Log the exception message
            return null;
        }
    }

    /** @param array<mixed> $data */
    protected function mapToOrder(array $data): Order
    {
        return new Order(
            orderId: $data['orderId'],
            createdAt: new \DateTimeImmutable($data['createdAt']),
            name: $data['name'],
            totalPrice: new Decimal((string)$data['totalPrice']),
            currency: CurrencyEnum::from((int)$data['currency']),
            status: OrderStatusEnum::from((int)$data['status']),
            items: \array_map(
                fn ($item) => new OrderItem(
                    orderItemId: $item['orderItemId'],
                    createdAt: new \DateTimeImmutable($item['createdAt']),
                    name: $item['name'],
                    pricePerUnit: new Decimal((string)$item['pricePerUnit']),
                    quantity: $item['quantity']
                ),
                $data['items'] ?? []
            )
        );
    }
}
