<?php

declare(strict_types=1);

namespace App\Controller\Common\Attribute;

use App\Controller\Common\Enum\HttpMethod;

/**
 * You can use this attribute multiple times on the same method.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Route // could be a readonly class, but I like explicity
{
    /** @param HttpMethod|list<HttpMethod> $method - single  method or a list of methods */
    public function __construct(
        public readonly string $path,
        public readonly HttpMethod|array $method,
    ) {
    }
}
