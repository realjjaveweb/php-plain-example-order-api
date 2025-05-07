<?php

declare(strict_types=1);

namespace App\Repository\Order;

use App\Enum\CurrencyEnum;
use App\Enum\Order\OrderStatusEnum;
use App\Model\Order\Order;
use App\Model\Order\OrderItem;
use Decimal\Decimal;
use PDO;

class MySQLOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly PDO $db,
    ) {
    }

    public function findById(int $id): ?Order
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE order_id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $orderData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($orderData === false) {
            return null;
        }

        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $orderData['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->mapToOrder($orderData);
    }

    /** @param array<mixed> $data */
    protected function mapToOrder(array $data): Order
    {
        return new Order(
            orderId: $data['order_id'],
            createdAt: new \DateTimeImmutable($data['created_at']),
            name: $data['name'],
            totalPrice: new Decimal((string)$data['total_price']),
            currency: CurrencyEnum::from((int)$data['currency']),
            status: OrderStatusEnum::from((int)$data['status']),
            items: \array_map(
                fn ($item) => new OrderItem(
                    orderItemId: $item['order_item_id'],
                    createdAt: new \DateTimeImmutable($item['created_at']),
                    name: $item['name'],
                    pricePerUnit: new Decimal((string)$item['price_per_unit']),
                    quantity: $item['quantity']
                ),
                $data['items'] ?? []
            )
        );
    }
}
