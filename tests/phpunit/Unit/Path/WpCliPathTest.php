<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Path;

use Syde\WpPhpUnitIntegration\Path\ComposerPackagePath;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPathNormalizer;
use Syde\WpPhpUnitIntegration\Path\WpCliPath;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

final class WpCliPathTest extends UnitTestCase
{
    public function testWpCliPathReturned(): void
    {
        $composerPackagePath = $this->createMock(ComposerPackagePath::class);
        $composerPackagePath
            ->method('installedPath')
            ->with('johnpbloch/wp-cli-phar')
            ->willReturn('/vendor/wp-cli');

        $localDependencyPathNormalizer = $this->createMock(LocalDependencyPathNormalizer::class);
        $localDependencyPathNormalizer
            ->method('normalize')
            ->willReturnArgument(0);

        $this->assertSame(
            '/vendor/wp-cli/wp',
            (new WpCliPath(
                $composerPackagePath,
                $localDependencyPathNormalizer,
            ))->path(),
        );
    }
}
