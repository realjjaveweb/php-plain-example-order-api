<?php

declare(strict_types=1);

namespace App\Controller\Common\Enum;

enum HttpMethod: string // ommitting Enum suffix for ease of use and readability
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
}
