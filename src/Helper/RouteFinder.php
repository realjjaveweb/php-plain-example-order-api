<?php

declare(strict_types=1);

namespace App\Helper;

use Psr\Container\ContainerInterface;
use App\Controller\Common\AbstractController;
use Slim\App;

class RouteFinder
{
    public const SRC_NAMESPACE = 'App';
    public const SRC_DIRECTORY_NAME = 'src';
    public const CONTROLLER_PATH = APP_ROOT . '/'.self::SRC_DIRECTORY_NAME.'/Controller';
    public const CONTROLLER_FILE_SUFFIX = 'Controller';
    public const CONTROLLER_FILE_EXTENSION = '.php';

    private const CACHE_DIRECTORY = APP_ROOT . '/var/cache/routeFinder';
    private const CACHE_FILE_CONTROLLERS = self::CACHE_DIRECTORY . '/controllers.cache.php';

    public function __construct()
    {
    }

    /**
     * Searches CONTROLLER_PATH (usually src/Controller) recursively for all classes extending AbstractController in *Controller.php files
     * @see \App\Controller\AbstractController
     * @see \App\Helper\RouteFinder::CONTROLLER_PATH
     * @param App<ContainerInterface> $app - App<ContainerInterface|null> (phpstan has a bug with this)
     * @throws \ReflectionException
     */
    public function findAndRegisterControllerRoutes(App $app): void
    {
        $cachedControllerClasses = $this->getCachedControllers();
        if ($cachedControllerClasses !== null) {
            foreach ($cachedControllerClasses as $controllerClass) {
                $this->registerControllerRoutes(
                    app: $app,
                    controllerClass: $controllerClass,
                );
            }
            return;
        }

        $controllerClasses = [];
        foreach ($this->getControllerDirectoryIterator() as $file) {
            if (!($file instanceof \SplFileInfo)) {
                throw new \RuntimeException(\RecursiveDirectoryIterator::class . 'did not return ' . \SplFileInfo::class);
            }

            // make sure it's a *Controller.php
            if (!\str_ends_with($file->getFilename(), self::CONTROLLER_FILE_SUFFIX . self::CONTROLLER_FILE_EXTENSION)) {
                continue;
            }

            // filepath => FQN of the class
            $controllerClass = $this->getControllerClassFqnByControllerFilepath($file->getPathname());

            // make sure it's SOME Controller, just not the AbstractController itself
            if (!is_subclass_of($controllerClass, AbstractController::class)) {
                continue;
            }

            $controllerClasses[] = $controllerClass;
            $this->registerControllerRoutes(
                app: $app,
                controllerClass: $controllerClass,
            );
        }

        $this->cacheControllers($controllerClasses);
    }

    /** @return \RecursiveIteratorIterator<\RecursiveDirectoryIterator> -  gets src/Controller tree iterator */
    private function getControllerDirectoryIterator(): \RecursiveIteratorIterator
    {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(self::CONTROLLER_PATH, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
    }

    /** @return string - parsed out class name with namespace (FQN) */
    private function getControllerClassFqnByControllerFilepath(string $controllerFilepath): string
    {
        // /src/ === App\ => [1] => Something\In\AppNamespace
        $namespacePathWithExt = \explode(\DIRECTORY_SEPARATOR.self::SRC_DIRECTORY_NAME.\DIRECTORY_SEPARATOR, $controllerFilepath)[1];
        $namespacePath = \preg_replace('/'.self::CONTROLLER_FILE_EXTENSION.'$/', '', $namespacePathWithExt); // remove .php from the end
        return self::SRC_NAMESPACE . '\\' . \str_replace(\DIRECTORY_SEPARATOR, '\\', $namespacePath);
    }

    /**
     * @param App<ContainerInterface> $app - App<ContainerInterface|null> (phpstan has a bug with this)
     * @param class-string<AbstractController> $controllerClass
     *
     * @throws \ReflectionException
     */
    private function registerControllerRoutes(App $app, string $controllerClass): void
    {
        // for generic registerRoutes purposes, we don't care about the constructor
        $controllerClassReflection = new \ReflectionClass($controllerClass);
        $controllerNoConstruct = $controllerClassReflection->newInstanceWithoutConstructor();
        // string is fine here, if method would not exist, it would throw the \ReflectionException
        $controllerClassReflection->getMethod('registerRoutes')->invoke($controllerNoConstruct, $app);
    }

    private function ensureCacheDirectoryExists(): void
    {
        if (!\is_dir(self::CACHE_DIRECTORY)) {
            \mkdir(self::CACHE_DIRECTORY, 0775, recursive: true);
        }
    }

    /** @param array<class-string<AbstractController>> $controllerClasses */
    private function cacheControllers(array $controllerClasses): void
    {
        $this->ensureCacheDirectoryExists();
        \file_put_contents(
            self::CACHE_FILE_CONTROLLERS,
            '<?php return ' . \var_export($controllerClasses, return: true) . ';'
        );
    }

    /** @return array<class-string<AbstractController>> */
    private function getCachedControllers(): ?array
    {
        if (!\file_exists(self::CACHE_FILE_CONTROLLERS)) {
            return null;
        }
        return require self::CACHE_FILE_CONTROLLERS;
    }
}
