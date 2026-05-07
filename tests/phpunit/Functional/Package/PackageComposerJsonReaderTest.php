<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Package;

use Syde\WpPhpunitIntegration\Package\PackageComposerJsonReader;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

final class PackageComposerJsonReaderTest extends FunctionalTestCase
{
    public function testReturnsComposerJsonContentWhenFileExists(): void
    {
        $this->filesystem->dumpFile($this->workspace . '/acme/composer.json', '{"name":"acme"}');

        $packageComposerJsonReader = new PackageComposerJsonReader(
            $this->localDependencyPath($this->workspace . '/acme'),
        );

        $this->assertSame(
            '{"name":"acme"}',
            $packageComposerJsonReader->read()
        );
    }

    public function testThrowsExceptionWhenFileDoesNotExist(): void
    {
        $this->expectException(\Throwable::class);

        (new PackageComposerJsonReader(
            $this->localDependencyPath($this->workspace . '/acme'),
        ))->read();
    }
}
