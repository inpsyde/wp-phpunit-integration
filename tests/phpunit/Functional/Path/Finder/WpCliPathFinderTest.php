<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Path\Finder;

use Syde\WpPhpUnitIntegration\Path\Finder\WpCliPathFinder;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

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
        $this->expectExceptionMessageMatches('/The matched WP-CLI binary is not executable/');

        $this->filesystem->appendToFile($this->workspace . '/acme/vendor/bin/wp', '');

        (new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        ))->find();
    }

    public function testFindLocatesWpCliExecutableWhenInVendorBin3(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessageMatches('/Could not locate WP-CLI binary/');

        $this->filesystem->mkdir($this->workspace . '/acme');

        (new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        ))->find();
    }

    public function testFindIgnoresSameNamedBinariesBundledInsideInstalledPlugins(): void
    {
        // Regression test: a WordPress plugin installed under wp-content/plugins can bundle
        // its own composer-generated "wp" bin proxy (e.g. WooCommerce's release zip does).
        // The finder must not be tricked by it into returning a broken, unrelated binary.
        $this->filesystem->appendToFile(
            $this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/plugins/some-plugin/vendor/bin/wp',
            '',
        );
        $this->filesystem->chmod(
            $this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/plugins/some-plugin/vendor/bin/wp',
            0755,
        );

        $this->filesystem->appendToFile($this->workspace . '/acme/vendor/bin/wp', '');
        $this->filesystem->chmod($this->workspace . '/acme/vendor/bin/wp', 0755);

        $autoDiscoveredWpCliPath = new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        );

        $this->assertSame(
            $this->workspace . '/acme/vendor/bin/wp',
            $autoDiscoveredWpCliPath->find(),
        );
    }
}
