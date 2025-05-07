<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\Enum\CurrencyEnum;
use App\Enum\Order\OrderStatusEnum;
use Decimal\Decimal;

class Order
{
    private int $orderId;
    private \DateTimeImmutable $createdAt;
    private string $name;
    private Decimal $totalPrice;
    private CurrencyEnum $currency;
    private OrderStatusEnum $status;
    /** @var array<OrderItem> $items */
    private array $items;

    /**
     * @param array<OrderItem> $items
     */
    public function __construct(
        int $orderId,
        \DateTimeImmutable $createdAt,
        string $name,
        Decimal $totalPrice,
        CurrencyEnum $currency,
        OrderStatusEnum $status,
        array $items = []
    ) {
        $this->orderId = $orderId;
        $this->createdAt = $createdAt;
        $this->name = $name;
        $this->totalPrice = $totalPrice;
        $this->currency = $currency;
        $this->status = $status;
        $this->items = $items;
    }

    public function getId(): int
    {
        return $this->orderId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTotalPrice(): Decimal
    {
        return $this->totalPrice;
    }

    public function getCurrency(): CurrencyEnum
    {
        return $this->currency;
    }

    public function getStatus(): OrderStatusEnum
    {
        return $this->status;
    }

    /** @return array<OrderItem> $items */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        // maybe using an existing serializer (like JMS) would be better
        return [
            'id' => $this->orderId,
            'createdAt' => $this->createdAt->format('c'),
            'name' => $this->name,
            'totalPrice' => $this->totalPrice,
            'currency' => $this->currency,
            'status' => $this->status,
            'items' => \array_map( // could be directly on the OrderItem
                static fn (OrderItem $item): array => [
                    'id' => $item->getOrderItemId(),
                    'createdAt' => $item->getCreatedAt()->format('c'),
                    'name' => $item->getName(),
                    'price_per_unit' => $item->getPricePerUnit(),
                    'quantity' => $item->getQuantity(),
                ],
                $this->items
            ),
        ];
    }
}
