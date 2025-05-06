<?php

declare(strict_types=1);

namespace App\Config\Db\MySQL;

use App\Config\Db\PdoConnectionConfigInterface;

class ConnectionConfig implements PdoConnectionConfigInterface
{
    /** @inheritdoc */
    public function getConfig(): array
    {
        return [
            // keys => argument names of \PDO constructor
            // https://www.php.net/manual/en/pdo.construct.php
            'dsn' => 'mysql:host=' . \getenv('DB_HOST') . ';dbname=' . \getenv('MYSQL_DATABASE'),
            'username' => \getenv('MYSQL_USER'),
            'password' => \getenv('MYSQL_PASSWORD'),
            'options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        ];
    }
}
