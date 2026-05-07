<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Path\Finder;

use Syde\WpPhpunitIntegration\EnvVar;

class CachedLocalDependencyPathFinder implements LocalDependencyPathFinder
{
    public function __construct(
        private readonly LocalDependencyPathFinder $localDependencyPathFinder,
        private readonly CachedLocalDependencyPathFinderKey $cachedLocalDependencyPathFinderKey,
        private readonly EnvVar $envVar,
    ) {
    }

    public function find(): string
    {
        $cacheKey = $this->cachedLocalDependencyPathFinderKey->generate($this->localDependencyPathFinder);
        $cachedPath = $this->envVar->get($cacheKey);

        if ($cachedPath !== null) {
            return $cachedPath;
        }

        $path = $this->localDependencyPathFinder->find();

        $this->envVar->set($cacheKey, $path);

        return $path;
    }
}
