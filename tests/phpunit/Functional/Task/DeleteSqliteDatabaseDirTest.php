<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

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

        (new DeleteSqliteDatabaseDir(
            $this->localDependencyPath($this->workspace . '/wordpress'),
        ))->execute();

        $this->assertDirectoryDoesNotExist($this->workspace . '/wordpress/wp-content/database');
    }
}
