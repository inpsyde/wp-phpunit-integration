<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Path;

use Exception;
use Syde\WpPhpUnitIntegration\Path\ComposerPackagePath;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPathNormalizer;
use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Path\WordPressPathFinder;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

final class WordPressPathTest extends UnitTestCase
{
    public function testReturnsInstalledPackagePathWithPriority(): void
    {
        $composerPackagePath = $this->createMock(ComposerPackagePath::class);
        $composerPackagePath
            ->expects($this->once())
            ->method('installPath')
            ->with('roots/wordpress')
            ->willReturn('/vendor/wordpress');

        $wordpressPathFinder = $this->createMock(WordPressPathFinder::class);
        $wordpressPathFinder
            ->expects($this->never())
            ->method('discoveredPath');

        $localDependencyPathNormalizer = $this->createMock(LocalDependencyPathNormalizer::class);
        $localDependencyPathNormalizer
            ->method('normalize')
            ->willReturnArgument(0);

        $this->assertSame(
            '/vendor/wordpress',
            (new WordPressPath(
                $composerPackagePath,
                $wordpressPathFinder,
                $localDependencyPathNormalizer,
            ))->path(),
        );
    }

    public function testFallsBackToDiscoveredPackagePath(): void
    {
        $composerPackagePath = $this->createMock(ComposerPackagePath::class);
        $composerPackagePath
            ->method('installPath')
            ->willThrowException(new Exception());

        $wordpressPathFinder = $this->createMock(WordPressPathFinder::class);
        $wordpressPathFinder
            ->expects($this->once())
            ->method('discoveredPath')
            ->willReturn('/wp');

        $localDependencyPathNormalizer = $this->createMock(LocalDependencyPathNormalizer::class);
        $localDependencyPathNormalizer
            ->method('normalize')
            ->willReturnArgument(0);

        $this->assertSame(
            '/wp',
            (new WordPressPath(
                $composerPackagePath,
                $wordpressPathFinder,
                $localDependencyPathNormalizer,
            ))->path(),
        );
    }
}
