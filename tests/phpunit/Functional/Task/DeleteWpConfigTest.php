<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Task\DeleteWpConfig;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class DeleteWpConfigTest extends FunctionalTestCase
{
    public function testRemovesWpConfigFileFromWpDir(): void
    {
        $this->filesystem->appendToFile($this->workspace . '/wordpress/wp-config.php', '');

        (new DeleteWpConfig(
            $this->createLocalDependencyPathStub(WordPressPath::class, $this->workspace . '/wordpress'),
        ))->execute();

        $this->assertFileDoesNotExist($this->workspace . '/wordpress/wp-config.php');
    }
}
