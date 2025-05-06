<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\CurrencyEnum;
use App\Enum\Order\OrderStatusEnum;

require_once __DIR__ . '/common.php';

// executed right after class def
class GenerateTestData
{
    private readonly \PDO $db;

    public function __construct()
    {
        cliMsg(__CLASS__ . ' running...', color: 'yellow');

        $this->db = new \PDO(...(new \App\Config\Db\MySQL\ConnectionConfig())->getConfig());
    }

    public function execute(): void
    {
        try {
            $this->generateData();
            cliMsg(__CLASS__ . ': Data generated successfully.', color: 'green');
        } catch (\Throwable $e) {
            cliMsg(__CLASS__ . ': Error generating data: ' . $e->getMessage(), color: 'red');
        }
    }

    private function generateData(): void
    {
        // the ID columns have Auto Increment, but for the testing purposes, we want to be explicit
        $sql = <<<SQL
            INSERT INTO orders (order_id, created_at, name, total_price, currency, status) VALUES
            (42, NOW(), 'Order 1', 171, :CurrencyEnum_USD, :OrderStatusEnum_Pending);
            INSERT INTO order_items (order_item_id, order_id, created_at, name, price_per_unit, quantity) VALUES
            (1, 42, NOW(), 'Item 1', 50.25, 1),
            (2, 42, NOW(), 'Item 2', 30.00, 2),
            (3, 42, NOW(), 'Item 3', 20.25, 3);

            INSERT INTO orders (order_id, created_at, name, total_price, currency, status) VALUES
            (50, NOW(), 'Order 2', 31.00, :CurrencyEnum_EUR, :OrderStatusEnum_Processing);
            INSERT INTO order_items (order_item_id, order_id, created_at, name, price_per_unit, quantity) VALUES
            (4, 50, NOW(), 'Item 1', 10.50, 1),
            (5, 50, NOW(), 'Item 2', 10.25, 2);

            INSERT INTO orders (order_id, created_at, name, total_price, currency, status) VALUES
            (33333, NOW(), 'Order 3', 150.25, :CurrencyEnum_CZK, :OrderStatusEnum_Completed);
            INSERT INTO order_items (order_item_id, order_id, created_at, name, price_per_unit, quantity) VALUES
            (6, 33333, NOW(), 'Item 1', 150.25, 1);
        SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':CurrencyEnum_USD', CurrencyEnum::USD->value, \PDO::PARAM_INT);
        $stmt->bindValue(':CurrencyEnum_EUR', CurrencyEnum::EUR->value, \PDO::PARAM_INT);
        $stmt->bindValue(':CurrencyEnum_CZK', CurrencyEnum::CZK->value, \PDO::PARAM_INT);
        $stmt->bindValue(':OrderStatusEnum_Pending', OrderStatusEnum::PENDING->value, \PDO::PARAM_INT);
        $stmt->bindValue(':OrderStatusEnum_Processing', OrderStatusEnum::PROCESSING->value, \PDO::PARAM_INT);
        $stmt->bindValue(':OrderStatusEnum_Completed', OrderStatusEnum::COMPLETED->value, \PDO::PARAM_INT);

        $stmt->execute();
    }
}

(new GenerateTestData())->execute();
