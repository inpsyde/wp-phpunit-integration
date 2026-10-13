<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Task\CreateEmptyWpThemesDir;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class CreateEmptyWpThemesDirTest extends FunctionalTestCase
{
    public function testCreatesEmptyThemesDirInWpContent(): void
    {
        $this->filesystem->mkdir($this->workspace . '/wordpress/wp-content');

        $wordPressPath = $this->createStub(WordPressPath::class);
        $wordPressPath
            ->method('path')
            ->willReturn($this->workspace . '/wordpress');

        $task = new CreateEmptyWpThemesDir(
            $wordPressPath,
        );
        $task->execute();


        $this->assertDirectoryExists($this->workspace . '/wordpress/wp-content/themes');
    }
}
