<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Task\DeleteSqliteDatabaseDir;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class DeleteSqliteDatabaseDirTest extends FunctionalTestCase
{
    public function testExecuteRecursivelyRemovesSqliteDatabaseDirFromWpContent(): void
    {
        $this->filesystem->appendToFile(
            $this->workspace . '/wordpress/wp-content/database/.ht.sqlite',
            '',
        );

        $wordPressPath = $this->createStub(WordPressPath::class);
        $wordPressPath
            ->method('path')
            ->willReturn($this->workspace . '/wordpress');

        (new DeleteSqliteDatabaseDir(
            $wordPressPath,
        ))->execute();

        $this->assertDirectoryDoesNotExist($this->workspace . '/wordpress/wp-content/database');
    }
}
