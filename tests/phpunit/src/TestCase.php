<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function localDependencyPath(string $path): LocalDependencyPath
    {
        return new class ($path) implements LocalDependencyPath {
            public function __construct(
                private readonly string $path,
            ) {
            }

            public function path(): string
            {
                return $this->path;
            }
        };
    }
}
