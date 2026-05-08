<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

class LocalDependencyPathNormalizer
{
    public function normalize(string $path): string
    {
        return rtrim($path, '/');
    }
}
