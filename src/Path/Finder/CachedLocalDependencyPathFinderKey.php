<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Path\Finder;

use ReflectionClass;

class CachedLocalDependencyPathFinderKey
{
    private const ENV_PREFIX = 'WP_LITE_HELPER_CACHED_LOCAL_DEPENDENCY_';

    public function generate(LocalDependencyPathFinder $localDependencyPathFinder): string
    {
        $classShortName = (new ReflectionClass($localDependencyPathFinder))->getShortName();

        return self::ENV_PREFIX . mb_strtoupper($classShortName);
    }
}
