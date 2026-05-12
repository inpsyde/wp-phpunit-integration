<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Path\Finder;

use Syde\WpPhpUnitIntegration\Path\Finder\CachedLocalDependencyPathFinderKey;
use Syde\WpPhpUnitIntegration\Path\Finder\LocalDependencyPathFinder;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

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
