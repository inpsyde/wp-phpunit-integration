<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Task;

use Syde\WpPhpunitIntegration\Task\DeleteWpDebugLog;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

final class DeleteWpDebugLogTest extends FunctionalTestCase
{
    public function testRemovesWpDebugLogFileFromWpContent(): void
    {
        $this->filesystem->appendToFile($this->workspace . '/wordpress/wp-content/debug.log', '');

        (new DeleteWpDebugLog(
            $this->localDependencyPath($this->workspace . '/wordpress'),
        ))->execute();

        $this->assertFileDoesNotExist($this->workspace . '/wordpress/wp-content/debug.log');
    }
}
