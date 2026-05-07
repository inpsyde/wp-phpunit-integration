<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Unit\Path\Finder;

use Syde\WpPhpunitIntegration\EnvVar;
use Syde\WpPhpunitIntegration\Path\Finder\CachedLocalDependencyPathFinder;
use Syde\WpPhpunitIntegration\Path\Finder\CachedLocalDependencyPathFinderKey;
use Syde\WpPhpunitIntegration\Path\Finder\LocalDependencyPathFinder;
use Syde\WpPhpunitIntegration\Tests\UnitTestCase;

final class CachedLocalDependencyPathFinderTest extends UnitTestCase
{
    public function testReturnsCachedValueWhenAvailable(): void
    {
        $localDependencyPathFinder = $this->createMock(LocalDependencyPathFinder::class);
        $localDependencyPathFinder->expects($this->never())->method('find');

        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->once())
            ->method('get')
            ->willReturn('/acme');
        $envVar
            ->expects($this->never())
            ->method('set');

        $cachedLocalDependencyPathFinderKey = $this->createStub(CachedLocalDependencyPathFinderKey::class);

        $result = (new CachedLocalDependencyPathFinder(
            $localDependencyPathFinder,
            $cachedLocalDependencyPathFinderKey,
            $envVar,
        ))->find();

        $this->assertSame(
            '/acme',
            $result,
        );
    }

    public function testFetchesAndCachesValueWhenNotAvailable(): void
    {
        $localDependencyPathFinder = $this->createMock(LocalDependencyPathFinder::class);
        $localDependencyPathFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn('/acme');

        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->once())
            ->method('get')->willReturn(null);
        $envVar
            ->expects($this->once())
            ->method('set')->with('SOME_KEY', '/acme');

        $cachedLocalDependencyPathFinderKey = $this->createMock(CachedLocalDependencyPathFinderKey::class);
        $cachedLocalDependencyPathFinderKey
            ->expects($this->once())
            ->method('generate')->willReturn('SOME_KEY');


        $result = (new CachedLocalDependencyPathFinder(
            $localDependencyPathFinder,
            $cachedLocalDependencyPathFinderKey,
            $envVar,
        ))->find();

        $this->assertSame(
            '/acme',
            $result,
        );
    }
}
