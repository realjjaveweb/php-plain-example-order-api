<?php

declare(strict_types=1);

namespace App\Enum;

enum CurrencyEnum: int
{
    case CZK = 1;
    case EUR = 2;
    case USD = 3;
}
