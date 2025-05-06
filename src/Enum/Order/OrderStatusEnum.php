<?php

declare(strict_types=1);

namespace App\Enum\Order;

enum OrderStatusEnum: int
{
    public const DEFAULT = self::NEW;

    // New order statuses
    case NEW = 1;

    // Order is being worked on statuses
    case PENDING = 5;
    case WAITING_FOR_PAYMENT = 10;
    case PROCESSING = 20;
    case ON_HOLD = 30;

    // Order is (anyhow) done statuses
    case CANCELED = 100;
    case COMPLETED = 110;
    case REFUNDED = 120;
}
