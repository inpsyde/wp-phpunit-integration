<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Task;

use Syde\WpPhpunitIntegration\Task\UnlinkTestedTheme;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

final class UnlinkTestedThemeTest extends FunctionalTestCase
{
    public function testExecuteRemovesThemeSymlinkFromWordPressThemesDirectory(): void
    {
        $this->filesystem->mkdir($this->workspace . '/acme');
        // Should this be a file?
        $this->filesystem->mkdir($this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/themes/acme');

        (new UnlinkTestedTheme(
            $this->localDependencyPath($this->workspace . '/acme'),
            $this->localDependencyPath($this->workspace . '/acme/vendor/wordpress/wordpress'),
        ))->execute();

        $this->assertDirectoryExists($this->workspace . '/acme');
        $this->assertDirectoryDoesNotExist($this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/themes/acme');
    }
}
