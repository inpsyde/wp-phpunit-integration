<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Path;

class LocalDependencyPathNormalizer
{
    public function normalize(string $path): string
    {
        return rtrim($path, '/');
    }
}
