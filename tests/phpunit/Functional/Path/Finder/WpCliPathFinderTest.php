<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Path\Finder;

use Syde\WpPhpUnitIntegration\EnvVar;
use Syde\WpPhpUnitIntegration\Path\Finder\WpCliPathFinder;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class WpCliPathFinderTest extends FunctionalTestCase
{
    /**
     * @param array<string, string> $values
     */
    private function envVar(array $values = []): EnvVar
    {
        $envVar = $this->createMock(EnvVar::class);
        $envVar->method('get')->willReturnCallback(
            static fn (string $key): ?string => $values[$key] ?? null,
        );

        return $envVar;
    }

    public function testFindLocatesWpCliExecutableWhenNotCustomized(): void
    {
        $this->filesystem->dumpFile($this->workspace . '/acme/composer.json', '{}');
        $this->filesystem->appendToFile($this->workspace . '/acme/vendor/bin/wp', '');
        $this->filesystem->chmod($this->workspace . '/acme/vendor/bin/wp', 0755);

        $autoDiscoveredWpCliPath = new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
            $this->envVar(),
        );

        $this->assertSame(
            $this->workspace . '/acme/vendor/bin/wp',
            $autoDiscoveredWpCliPath->find()
        );
    }

    public function testFindThrowsExceptionWhenFileIsNotExecutable(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessageMatches('/The matched WP-CLI binary is not executable/');

        $this->filesystem->dumpFile($this->workspace . '/acme/composer.json', '{}');
        $this->filesystem->appendToFile($this->workspace . '/acme/vendor/bin/wp', '');

        (new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
            $this->envVar(),
        ))->find();
    }

    public function testFindThrowsExceptionWhenFileNotFound(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessageMatches('/Could not locate WP-CLI binary/');

        $this->filesystem->dumpFile($this->workspace . '/acme/composer.json', '{}');

        (new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
            $this->envVar(),
        ))->find();
    }

    public function testFindIgnoresSameNamedBinariesBundledInsideInstalledPlugins(): void
    {
        // Regression test: a WordPress plugin installed under wp-content/plugins can bundle
        // its own composer-generated "wp" bin proxy (e.g. WooCommerce's release zip does).
        // The finder must not be tricked by it into returning a broken, unrelated binary.
        $this->filesystem->dumpFile($this->workspace . '/acme/composer.json', '{}');

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
            $this->envVar(),
        );

        $this->assertSame(
            $this->workspace . '/acme/vendor/bin/wp',
            $autoDiscoveredWpCliPath->find(),
        );
    }

    public function testFindHonoursComposerBinDirEnvironmentVariable(): void
    {
        $this->filesystem->dumpFile($this->workspace . '/acme/composer.json', '{}');
        $this->filesystem->appendToFile($this->workspace . '/acme/scripts/wp', '');
        $this->filesystem->chmod($this->workspace . '/acme/scripts/wp', 0755);

        $autoDiscoveredWpCliPath = new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
            $this->envVar(['COMPOSER_BIN_DIR' => $this->workspace . '/acme/scripts']),
        );

        $this->assertSame(
            $this->workspace . '/acme/scripts/wp',
            $autoDiscoveredWpCliPath->find(),
        );
    }

    public function testFindHonoursBinDirConfiguredInComposerJson(): void
    {
        $this->filesystem->dumpFile(
            $this->workspace . '/acme/composer.json',
            '{"config":{"bin-dir":"scripts"}}',
        );
        $this->filesystem->appendToFile($this->workspace . '/acme/scripts/wp', '');
        $this->filesystem->chmod($this->workspace . '/acme/scripts/wp', 0755);

        $autoDiscoveredWpCliPath = new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
            $this->envVar(),
        );

        $this->assertSame(
            $this->workspace . '/acme/scripts/wp',
            $autoDiscoveredWpCliPath->find(),
        );
    }

    public function testFindHonoursVendorDirConfiguredInComposerJsonForTheDefaultBinDir(): void
    {
        $this->filesystem->dumpFile(
            $this->workspace . '/acme/composer.json',
            '{"config":{"vendor-dir":"libs"}}',
        );
        $this->filesystem->appendToFile($this->workspace . '/acme/libs/bin/wp', '');
        $this->filesystem->chmod($this->workspace . '/acme/libs/bin/wp', 0755);

        $autoDiscoveredWpCliPath = new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
            $this->envVar(),
        );

        $this->assertSame(
            $this->workspace . '/acme/libs/bin/wp',
            $autoDiscoveredWpCliPath->find(),
        );
    }

    public function testFindPrefersComposerBinDirEnvironmentVariableOverComposerJsonConfig(): void
    {
        $this->filesystem->dumpFile(
            $this->workspace . '/acme/composer.json',
            '{"config":{"bin-dir":"scripts"}}',
        );
        $this->filesystem->appendToFile($this->workspace . '/acme/env-scripts/wp', '');
        $this->filesystem->chmod($this->workspace . '/acme/env-scripts/wp', 0755);

        $autoDiscoveredWpCliPath = new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
            $this->envVar(['COMPOSER_BIN_DIR' => $this->workspace . '/acme/env-scripts']),
        );

        $this->assertSame(
            $this->workspace . '/acme/env-scripts/wp',
            $autoDiscoveredWpCliPath->find(),
        );
    }
}
