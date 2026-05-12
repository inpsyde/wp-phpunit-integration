<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Container;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

readonly class SimplestContainer implements ContainerInterface
{
    /**
     * @param array<class-string, callable(ContainerInterface): object> $serviceDefinitions
     */
    public function __construct(
        private array $serviceDefinitions,
    ) {
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new class extends InvalidArgumentException implements NotFoundExceptionInterface {
            };
        }

        return $this->serviceDefinitions[$id]($this);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->serviceDefinitions);
    }
}
