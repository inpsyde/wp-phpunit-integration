<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Task;

use Syde\WpPhpunitIntegration\Task\DeleteWpConfig;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

final class DeleteWpConfigTest extends FunctionalTestCase
{
    public function testRemovesWpConfigFileFromWpDir(): void
    {
        $this->filesystem->appendToFile($this->workspace . '/wordpress/wp-config.php', '');

        (new DeleteWpConfig(
            $this->localDependencyPath($this->workspace . '/wordpress'),
        ))->execute();

        $this->assertFileDoesNotExist($this->workspace . '/wordpress/wp-config.php');
    }
}
