<?php

declare(strict_types=1);

namespace App\Controller\Common;

use Slim\App;
use App\Controller\Common\Attribute\Route;
use App\Controller\Common\Enum\HttpMethod;

abstract class AbstractController
{
    public function __construct()
    {
    }
    /** @param App<\Psr\Container\ContainerInterface|null> $app */
    final public function registerRoutes(App $app): void
    {
        foreach ($this->getRouteAttributes() as $classMethod => $route) {
            $requestMethodCases = \is_array($route->method) ? $route->method : [$route->method];
            $requestMethodStrings = \array_map(
                static fn (HttpMethod $requestMethod) => $requestMethod->value,
                $requestMethodCases,
            );

            $app->map(
                methods: $requestMethodStrings,
                pattern: $route->path,
                // following does not work with DI/autowiring
                // callable: $this->$classMethod(...),
                callable: [$this::class, $classMethod],
            );
        }
    }

    /** @return array<string,Route> - Route attribute instances keyed by their class method */
    final protected function getRouteAttributes(): array
    {
        $attrReflection = new \ReflectionClass($this);

        $routes = [];
        foreach ($attrReflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $classMethod) {
            foreach ($classMethod->getAttributes(Route::class) as $routeAttr) {
                $routes[$classMethod->name] = $routeAttr->newInstance();
            }
        }

        return $routes;
    }
}
