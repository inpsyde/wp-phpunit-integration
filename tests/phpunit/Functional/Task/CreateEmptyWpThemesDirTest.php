<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Task;

use Syde\WpPhpunitIntegration\Task\CreateEmptyWpThemesDir;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

final class CreateEmptyWpThemesDirTest extends FunctionalTestCase
{
    public function testCreatesEmptyThemesDirInWpContent(): void
    {
        $this->filesystem->mkdir($this->workspace . '/wordpress/wp-content');

        $task = new CreateEmptyWpThemesDir(
            $this->localDependencyPath($this->workspace . '/wordpress'),
        );
        $task->execute();


        $this->assertDirectoryExists($this->workspace . '/wordpress/wp-content/themes');
    }
}
