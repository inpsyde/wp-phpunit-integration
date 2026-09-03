<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Path\Finder;

use Syde\WpPhpUnitIntegration\Path\Finder\WpCliPathFinder;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class WpCliPathFinderTest extends FunctionalTestCase
{
    public function testWpCliIsLocatedUnderTypicalBin(): void
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

    public function testWpContentDirectoryIsIgnoredWhenWpCliIsLocated(): void
    {
        $this->filesystem->appendToFile(
            // `_` is used to force to be the first matched
            $this->workspace . '/acme/vendor/_/wp-content/plugins/some-plugin/vendor/bin/wp',
            '',
        );
        $this->filesystem->chmod(
            $this->workspace . '/acme/vendor/_/wp-content/plugins/some-plugin/vendor/bin/wp',
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

    public function testThrowsWhenLocatedWpCliIsNotExecutable(): void
    {
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->markTestSkipped('Windows has no POSIX file permissions to test.');
        }

        $this->expectExceptionMessageMatches('/The matched WP-CLI binary is not executable/');

        $this->filesystem->appendToFile($this->workspace . '/acme/vendor/bin/wp', '');
        $this->filesystem->chmod($this->workspace . '/acme/vendor/bin/wp', 0644);

        (new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        ))->find();
    }

    public function testThrowsWhenNoWpCliIsLocated(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessageMatches('/Could not locate WP-CLI binary/');

        $this->filesystem->mkdir($this->workspace . '/acme');

        (new WpCliPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        ))->find();
    }
}
