<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Functional\Path\Finder;

use Syde\PocWpLiteIntegrationTestHelper\Path\Finder\WordPressPathFinder;
use Syde\PocWpLiteIntegrationTestHelper\Tests\FunctionalTestCase;

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
            $autoDiscoveredWordPressPath->find(),
        );
    }

    public function testThrowsExceptionWhenWordPressNotFound(): void
    {
        $this->expectExceptionMessage('Could not locate WordPress installation.');

        $this->filesystem->mkdir($this->workspace . '/acme');

        (new WordPressPathFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        ))->find();
    }
}
