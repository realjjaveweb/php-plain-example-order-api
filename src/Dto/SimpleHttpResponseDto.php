<?php

declare(strict_types=1);

namespace App\Dto;

readonly class SimpleHttpResponseDto
{
    /**
     * @param array<mixed>|object $body
     */
    public function __construct(
        public int $statusCode,
        public array|object $body,
    ) {
    }
}
