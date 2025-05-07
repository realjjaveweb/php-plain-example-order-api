<?php

declare(strict_types=1);

namespace App\Model\Order;

use Decimal\Decimal;

class OrderItem
{
    private int $orderItemId;
    private \DateTimeImmutable $createdAt;
    private string $name;
    private Decimal $pricePerUnit; // Currency is based on the Order
    private int $quantity;

    public function __construct(
        int $orderItemId,
        \DateTimeImmutable $createdAt,
        string $name,
        Decimal $pricePerUnit,
        int $quantity,
    ) {
        $this->orderItemId = $orderItemId;
        $this->createdAt = $createdAt;
        $this->name = $name;
        $this->pricePerUnit = $pricePerUnit;
        $this->quantity = $quantity;
    }

    public function getOrderItemId(): int
    {
        return $this->orderItemId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPricePerUnit(): Decimal
    {
        return $this->pricePerUnit;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
