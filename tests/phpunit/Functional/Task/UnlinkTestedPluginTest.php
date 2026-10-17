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

        $packageRootPath = $this->createStub(PackageRootPath::class);
        $packageRootPath
            ->method('path')
            ->willReturn($this->workspace . '/acme');

        $wordPressPath = $this->createStub(WordPressPath::class);
        $wordPressPath
            ->method('path')
            ->willReturn($this->workspace . '/acme/vendor/wordpress/wordpress');

        (new UnlinkTestedPlugin(
            $packageRootPath,
            $wordPressPath,
        ))->execute();

        $this->assertDirectoryExists($this->workspace . '/acme');
        $this->assertDirectoryDoesNotExist(
            $this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/plugins/acme',
        );
    }
}
