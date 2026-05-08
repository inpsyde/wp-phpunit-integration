<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Container;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ServiceLocator
{
    private static ?ContainerInterface $container = null;

    private function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public static function init(ContainerInterface $container): void
    {
        if (self::$container instanceof \Psr\Container\ContainerInterface) {
            throw new Exception('The ServiceLocator container is already initialized.');
        }

        self::$container = $container;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public static function retrieve(string $id): mixed
    {
        if (!self::$container instanceof \Psr\Container\ContainerInterface) {
            throw new Exception('The ServiceLocator container is not initialized.');
        }

        return self::$container->get($id);
    }
}
