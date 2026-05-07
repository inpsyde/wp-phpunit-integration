<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Unit\Path\Finder;

use Syde\PocWpLiteIntegrationTestHelper\Path\Finder\CachedLocalDependencyPathFinderKey;
use Syde\PocWpLiteIntegrationTestHelper\Path\Finder\LocalDependencyPathFinder;
use Syde\PocWpLiteIntegrationTestHelper\Tests\UnitTestCase;

final class CachedLocalDependencyPathFinderKeyTest extends UnitTestCase
{
    public function testGeneratesExpectedCacheKeyPrefix(): void
    {
        $this->assertStringStartsWith(
            'WP_LITE_HELPER_CACHED_LOCAL_DEPENDENCY_MOCKOBJECT_LOCALDEPENDENCYPATHFINDER',
            (new CachedLocalDependencyPathFinderKey())->generate($this->createStub(LocalDependencyPathFinder::class)),
        );
    }
}
