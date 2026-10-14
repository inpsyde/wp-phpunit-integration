<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Path\PackageRootPath;
use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Task\SymlinkTestedTheme;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class SymlinkTestedThemeTest extends FunctionalTestCase
{
    public function testExecuteCreatesSymlinkToTestedThemeInWordPressThemesDirectory(): void
    {
        $this->filesystem->mkdir($this->workspace . '/acme');
        $this->filesystem->dumpFile($this->workspace . '/acme/style.css', 'Acme Theme');
        $this->filesystem->mkdir($this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/themes/');

        (new SymlinkTestedTheme(
            $this->createLocalDependencyPathStub(
                PackageRootPath::class,
                $this->workspace . '/acme',
            ),
            $this->createLocalDependencyPathStub(
                WordPressPath::class,
                $this->workspace . '/acme/vendor/wordpress/wordpress',
            ),
        ))->execute();

        $this->assertDirectoryExists($this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/themes/acme');
        $this->assertStringEqualsFile(
            $this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/themes/acme/style.css',
            'Acme Theme',
        );
    }
}
