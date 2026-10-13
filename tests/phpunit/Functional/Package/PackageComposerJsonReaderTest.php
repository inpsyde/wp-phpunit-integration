<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Package;

use Syde\WpPhpUnitIntegration\Package\PackageComposerJsonReader;
use Syde\WpPhpUnitIntegration\Path\PackageRootPath;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class PackageComposerJsonReaderTest extends FunctionalTestCase
{
    public function testReturnsComposerJsonContentWhenFileExists(): void
    {
        $this->filesystem->dumpFile($this->workspace . '/acme/composer.json', '{"name":"acme"}');

        $packageRootPath = $this->createStub(PackageRootPath::class);
        $packageRootPath
            ->method('path')
            ->willReturn($this->workspace . '/acme');

        $packageComposerJsonReader = new PackageComposerJsonReader(
            $packageRootPath,
        );

        $this->assertSame(
            '{"name":"acme"}',
            $packageComposerJsonReader->read(),
        );
    }

    public function testThrowsExceptionWhenFileDoesNotExist(): void
    {
        $this->expectException(\Throwable::class);

        $packageRootPath = $this->createStub(PackageRootPath::class);
        $packageRootPath
            ->method('path')
            ->willReturn($this->workspace . '/acme');

        (new PackageComposerJsonReader(
            $packageRootPath,
        ))->read();
    }
}
