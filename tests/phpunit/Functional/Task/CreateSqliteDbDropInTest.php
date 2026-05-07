<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Task;

use Syde\WpPhpunitIntegration\Task\CreateSqliteDbDropIn;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

final class CreateSqliteDbDropInTest extends FunctionalTestCase
{
    public function testExecuteCopiesDbCopyFileToWordPressContentDirectoryWithCorrectPath(): void
    {
        $this->filesystem->mkdir($this->workspace . '/vendor/roots/wordpress/wp-content');
        $this->filesystem->dumpFile(
            $this->workspace . '/wp-content/plugins/sqlite-database-integration/db.copy',
            '{SQLITE_IMPLEMENTATION_FOLDER_PATH}',
        );

        (new CreateSqliteDbDropIn(
            $this->localDependencyPath($this->workspace . '/vendor/roots/wordpress'),
            $this->localDependencyPath($this->workspace . '/wp-content/plugins/sqlite-database-integration'),
        ))->execute();

        $this->assertFileExists($this->workspace . '/vendor/roots/wordpress/wp-content/db.php');
        $this->assertStringEqualsFile(
            $this->workspace . '/vendor/roots/wordpress/wp-content/db.php',
            $this->workspace . '/wp-content/plugins/sqlite-database-integration',
        );
    }
}
