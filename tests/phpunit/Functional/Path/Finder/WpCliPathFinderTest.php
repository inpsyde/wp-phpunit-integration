<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Path\Finder;

use Syde\WpPhpunitIntegration\Path\Finder\WpCliPathFinder;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

final class WpCliPathFinderTest extends FunctionalTestCase
{
    public function testFindLocatesWpCliExecutableWhenInVendorBin(): void
    {
        $this->filesystem->appendToFile($this->workspace . '/acme/vendor/bin/wp', '');
        $this->filesystem->chmod($this->workspace . '/acme/vendor/bin/wp', 0755);

        $autoDiscoveredWpCliPath = new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        );

        $this->assertSame(
            $this->workspace . '/acme/vendor/bin/wp',
            $autoDiscoveredWpCliPath->find()
        );
    }

    public function testFindLocatesWpCliExecutableWhenInVendorBin2(): void
    {
        $this->expectExceptionMessage('The matched WP-CLI binary is not executable.');

        $this->filesystem->appendToFile($this->workspace . '/acme/vendor/bin/wp', '');

        (new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        ))->find();
    }

    public function testFindLocatesWpCliExecutableWhenInVendorBin3(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessage('Could not locate WP-CLI binary.');

        $this->filesystem->mkdir($this->workspace . '/acme');

        (new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        ))->find();
    }
}
