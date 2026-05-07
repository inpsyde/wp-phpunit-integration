<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Task;

use Syde\WpPhpunitIntegration\Task\SymlinkTestedTheme;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

final class SymlinkTestedThemeTest extends FunctionalTestCase
{
    public function testExecuteCreatesSymlinkToTestedThemeInWordPressThemesDirectory(): void
    {
        $this->filesystem->mkdir($this->workspace . '/acme');
        $this->filesystem->dumpFile($this->workspace . '/acme/style.css', 'Acme Theme');
        $this->filesystem->mkdir($this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/themes/');

        (new SymlinkTestedTheme(
            $this->localDependencyPath($this->workspace . '/acme'),
            $this->localDependencyPath($this->workspace . '/acme/vendor/wordpress/wordpress'),
        ))->execute();

        $this->assertDirectoryExists($this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/themes/acme');
        $this->assertStringEqualsFile(
            $this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/themes/acme/style.css',
            'Acme Theme',
        );
    }
}
