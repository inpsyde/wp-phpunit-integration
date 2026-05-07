<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Functional\Task;

use Syde\PocWpLiteIntegrationTestHelper\Task\DeleteSqliteDatabaseDir;
use Syde\PocWpLiteIntegrationTestHelper\Tests\FunctionalTestCase;

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
