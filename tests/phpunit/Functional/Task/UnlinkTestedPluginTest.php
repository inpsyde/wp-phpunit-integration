<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Path\PackageRootPath;
use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Task\UnlinkTestedPlugin;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class UnlinkTestedPluginTest extends FunctionalTestCase
{
    public function testExecuteRemovesPluginSymlinkFromWordPressPluginsDirectory(): void
    {
        $this->filesystem->mkdir($this->workspace . '/acme');
        // Should this be a file?
        $this->filesystem->mkdir($this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/plugins/acme');

        (new UnlinkTestedPlugin(
            $this->createLocalDependencyPathStub(PackageRootPath::class, $this->workspace . '/acme'),
            $this->createLocalDependencyPathStub(
                WordPressPath::class,
                $this->workspace . '/acme/vendor/wordpress/wordpress',
            ),
        ))->execute();

        $this->assertDirectoryExists($this->workspace . '/acme');
        $this->assertDirectoryDoesNotExist(
            $this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/plugins/acme',
        );
    }
}
