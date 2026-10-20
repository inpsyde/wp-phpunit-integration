<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Task\DeleteWpDebugLog;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class DeleteWpDebugLogTest extends FunctionalTestCase
{
    public function testRemovesWpDebugLogFileFromWpContent(): void
    {
        $this->filesystem->appendToFile($this->workspace . '/wordpress/wp-content/debug.log', '');

        $wordPressPath = $this->createStub(WordPressPath::class);
        $wordPressPath
            ->method('path')
            ->willReturn($this->workspace . '/wordpress');

        (new DeleteWpDebugLog(
            $wordPressPath,
        ))->execute();

        $this->assertFileDoesNotExist($this->workspace . '/wordpress/wp-content/debug.log');
    }
}
