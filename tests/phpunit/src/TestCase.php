<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @template T of LocalDependencyPath
     * @param class-string<T> $class
     * @return T
     */
    protected function createLocalDependencyPathStub(string $class, string $path): LocalDependencyPath
    {
        $stub = $this->createStub($class);
        $stub->method('path')->willReturn($path);

        return $stub;
    }
}
