<?php

declare(strict_types=1);

namespace App\Command;

require_once __DIR__ . '/common.php';

// executed right after class def
class CreateSchema
{
    private readonly \PDO $db;

    public function __construct()
    {
        cliMsg(__CLASS__ . ' running...', );

        $this->db = new \PDO(...(new \App\Config\Db\MySQL\ConnectionConfig())->getConfig());
    }

    public function execute(): void
    {
        try {
            $this->createSchema();
            cliMsg(__CLASS__ . ': Schema created successfully.', color: 'green');
        } catch (\Throwable $e) {
            cliMsg(__CLASS__ . ': Error creating schema: ' . $e->getMessage(), color: 'red');
        }
    }

    private function createSchema(): void
    {
        $schemaSql = <<<SQL
        CREATE TABLE orders (
            order_id INT AUTO_INCREMENT PRIMARY KEY,
            created_at DATETIME NOT NULL,
            name VARCHAR(200) NOT NULL,
            total_price DECIMAL(15, 2) NOT NULL,
            currency TINYINT(3) NOT NULL,
            status TINYINT(3) NOT NULL,
            INDEX (created_at)
        );

        CREATE TABLE order_items (
            order_item_id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            created_at DATETIME NOT NULL,
            name VARCHAR(300) NOT NULL,
            price_per_unit DECIMAL(15, 2) NOT NULL,
            quantity BIGINT NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
            INDEX (created_at)
        );
        SQL;

        $this->db->exec($schemaSql);
    }
}

(new CreateSchema())->execute();
