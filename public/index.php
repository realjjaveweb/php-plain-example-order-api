<?php

declare(strict_types=1);

// error reporting&display OFF by default
error_reporting(0);
ini_set('display_errors', '0');

require_once __DIR__ . '/../vendor/autoload.php';

use App\Helper\RouteFinder;
use Slim\Factory\AppFactory;

// $app = AppFactory::create(); // original Slim factory
$app = \DI\Bridge\Slim\Bridge::create(
    new DI\Container([
        // controller
        App\Controller\Api\Order\OrderController::class => \DI\autowire(),
        App\Service\Order\OrderService::class => \DI\autowire(),
        App\Service\TranslationService::class => \DI\autowire(),
        App\Repository\Order\OrderRepositoryInterface::class => \DI\autowire(App\Repository\Order\MySQLOrderRepository::class),
        \PDO::class => \DI\create()->constructor(...(new \App\Config\Db\MySQL\ConnectionConfig())->getConfig()),
    ]),
);


(new RouteFinder())->findAndRegisterControllerRoutes($app);

// prettier http exceptions <3
$app->addErrorMiddleware(
    displayErrorDetails: false,
    logErrors: true,
    logErrorDetails: true,
    logger: null
);

// TODO: SOME AUTHORIZATION MIDDLEWARE WOULD BE NICE HERE

$app->run();
