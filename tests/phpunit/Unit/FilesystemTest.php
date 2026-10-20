<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit;

use BadMethodCallException;
use Syde\WpPhpUnitIntegration\Filesystem;
use Syde\WpPhpUnitIntegration\Path\PackageRootPath;
use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

final class FilesystemTest extends UnitTestCase
{
    public function testMethodThatDoesNotExistsOnSymfonyFilesystemThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);

        $filesystem = new Filesystem(
            $this->createStub(\Symfony\Component\Filesystem\Filesystem::class),
            $this->createStub(PackageRootPath::class),
            $this->createStub(WordPressPath::class),
        );

        /** @phpstan-ignore method.notFound */
        $filesystem->methodThatDoesNotExists();
    }

    public function testUnderlyingSymfonyFilesystemMethodIsCalled(): void
    {
        $symfonyFilesystem = $this->createMock(\Symfony\Component\Filesystem\Filesystem::class);
        $symfonyFilesystem
            ->expects($this->once())
            ->method('exists')
            ->with('foo');

        $filesystem = new Filesystem(
            $symfonyFilesystem,
            $this->createStub(PackageRootPath::class),
            $this->createStub(WordPressPath::class),
        );

        $filesystem->exists('foo');
    }
}
