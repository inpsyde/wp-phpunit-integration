<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path\Finder;

use Syde\WpPhpUnitIntegration\EnvVar;

readonly class CachedLocalDependencyPathFinder implements LocalDependencyPathFinder
{
    public function __construct(
        private LocalDependencyPathFinder $localDependencyPathFinder,
        private CachedLocalDependencyPathFinderKey $cachedLocalDependencyPathFinderKey,
        private EnvVar $envVar,
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
