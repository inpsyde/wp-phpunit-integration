<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Task\SymlinkTestedPlugin;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class SymlinkTestedPluginTest extends FunctionalTestCase
{
    public function testExecuteCreatesSymlinkToTestedPluginInWordPressPluginsDirectory(): void
    {
        $this->filesystem->mkdir($this->workspace . '/acme');
        $this->filesystem->dumpFile($this->workspace . '/acme/acme.php', 'Acme Plugin');
        $this->filesystem->mkdir($this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/plugins/');

        (new SymlinkTestedPlugin(
            $this->localDependencyPath($this->workspace . '/acme'),
            $this->localDependencyPath($this->workspace . '/acme/vendor/wordpress/wordpress'),
        ))->execute();

        $this->assertDirectoryExists($this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/plugins/acme');
        $this->assertStringEqualsFile(
            $this->workspace . '/acme/vendor/wordpress/wordpress/wp-content/plugins/acme/acme.php',
            'Acme Plugin',
        );
    }
}
