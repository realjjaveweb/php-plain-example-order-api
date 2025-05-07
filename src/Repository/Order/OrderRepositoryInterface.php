<?php

declare(strict_types=1);

namespace App\Repository\Order;

use App\Model\Order\Order;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;
}
