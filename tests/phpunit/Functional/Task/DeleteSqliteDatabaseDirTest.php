<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Task;

use Syde\WpPhpunitIntegration\Task\DeleteSqliteDatabaseDir;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

final class DeleteSqliteDatabaseDirTest extends FunctionalTestCase
{
    public function testExecuteRecursivelyRemovesSqliteDatabaseDirFromWpContent(): void
    {
        $this->filesystem->appendToFile(
            $this->workspace . '/wordpress/wp-content/database/.ht.sqlite',
            '',
        );

        (new DeleteSqliteDatabaseDir(
            $this->localDependencyPath($this->workspace . '/wordpress'),
        ))->execute();

        $this->assertDirectoryDoesNotExist($this->workspace . '/wordpress/wp-content/database');
    }
}
