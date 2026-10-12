<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Path;

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

        $autoDiscoveredWordPressPath = new WordPressPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        );

        $this->assertSame(
            $this->workspace . '/acme/vendor/roots/wordpress',
            $autoDiscoveredWordPressPath->discoveredPath(),
        );
    }

    public function testThrowsExceptionWhenWordPressNotFound(): void
    {
        $this->expectExceptionMessageMatches('/Could not locate WordPress installation/');

        $this->filesystem->mkdir($this->workspace . '/acme');

        (new WordPressPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        ))->discoveredPath();
    }
}
