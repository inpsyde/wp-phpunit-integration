<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Path;

use Syde\WpPhpUnitIntegration\Path\PackageRootPath;
use Syde\WpPhpUnitIntegration\Path\WordPressPathFinder;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class WordPressPathFinderTest extends FunctionalTestCase
{
    public function testLocatesWordPressPathViaWpConfigSample(): void
    {
        $this->filesystem->appendToFile(
            $this->workspace . '/acme/vendor/roots/wordpress/wp-config-sample.php',
            '',
        );

        $wordPressPathFinder = new WordPressPathFinder(
            $this->createLocalDependencyPathStub(
                PackageRootPath::class,
                $this->workspace . '/acme',
            ),
        );

        $this->assertSame(
            $this->workspace . '/acme/vendor/roots/wordpress',
            $wordPressPathFinder->discoveredPath(),
        );
    }

    public function testThrowsExceptionWhenWordPressNotFound(): void
    {
        $this->expectExceptionMessageMatches('/Could not locate WordPress installation/');

        $this->filesystem->mkdir($this->workspace . '/acme');

        (new WordPressPathFinder(
            $this->createLocalDependencyPathStub(
                PackageRootPath::class,
                $this->workspace . '/acme',
            ),
        ))->discoveredPath();
    }
}
