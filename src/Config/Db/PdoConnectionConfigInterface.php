<?php

declare(strict_types=1);

namespace App\Config\Db;

interface PdoConnectionConfigInterface
{
    // In PHP8.4, this could be done with declaring properties explicitly
    // and using \IteratorAggregate + get_object_vars()
    /** @return array<string,string|array<int,int>> - returns array that can be expanded with "..." to the \PDO::__construct() */
    public function getConfig(): array;
}
