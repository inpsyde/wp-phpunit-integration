<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Path;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPathNormalizer;
use Syde\WpPhpUnitIntegration\Path\PackageRootPath;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class PackageRootPathTest extends FunctionalTestCase
{
    public function testThrowsExceptionForNonExistentPath(): void
    {
        $this->expectException(\Throwable::class);

        new PackageRootPath(
            'random string',
            $this->createStub(LocalDependencyPathNormalizer::class),
        );
    }

    public function testReturnsNormalizedPathWhenDirectoryExists(): void
    {
        $this->filesystem->mkdir($this->workspace . '/acme');

        $localDependencyPathNormalizer = $this->createStub(LocalDependencyPathNormalizer::class);
        $localDependencyPathNormalizer->method('normalize')
            ->willReturnArgument(0);

        $packageRootPath = new PackageRootPath(
            $this->workspace . '/acme',
            $localDependencyPathNormalizer,
        );

        $this->assertSame(
            $this->workspace . '/acme',
            $packageRootPath->path()
        );
    }
}
