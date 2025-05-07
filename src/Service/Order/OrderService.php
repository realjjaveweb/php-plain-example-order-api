<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Repository\Order\OrderRepositoryInterface;
use App\Model\Order\Order;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function getOrderDetails(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }
}
