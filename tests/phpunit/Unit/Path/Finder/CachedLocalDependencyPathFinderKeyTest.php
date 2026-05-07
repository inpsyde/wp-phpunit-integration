<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Unit\Path\Finder;

use Syde\WpPhpunitIntegration\Path\Finder\CachedLocalDependencyPathFinderKey;
use Syde\WpPhpunitIntegration\Path\Finder\LocalDependencyPathFinder;
use Syde\WpPhpunitIntegration\Tests\UnitTestCase;

final class CachedLocalDependencyPathFinderKeyTest extends UnitTestCase
{
    public function testGeneratesExpectedCacheKeyPrefix(): void
    {
        $cacheKey = (new CachedLocalDependencyPathFinderKey())->generate($this->createStub(LocalDependencyPathFinder::class));

        $this->assertStringStartsWith(
            'WP_PHPUNIT_INTEGRATION_CACHED_LOCAL_DEPENDENCY_',
            $cacheKey,
        );
        $this->assertStringContainsString(
            'LOCALDEPENDENCYPATHFINDER',
            $cacheKey,
        );
    }
}
