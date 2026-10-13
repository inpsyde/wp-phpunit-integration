<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Path\SqliteDatabaseIntegrationPluginPath;
use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Task\CreateSqliteDbDropIn;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class CreateSqliteDbDropInTest extends FunctionalTestCase
{
    public function testExecuteCopiesDbCopyFileToWordPressContentDirectoryWithCorrectPath(): void
    {
        $this->filesystem->mkdir($this->workspace . '/vendor/roots/wordpress/wp-content');
        $this->filesystem->dumpFile(
            $this->workspace . '/wp-content/plugins/sqlite-database-integration/db.copy',
            '{SQLITE_IMPLEMENTATION_FOLDER_PATH}',
        );

        $wordPressPath = $this->createStub(WordPressPath::class);
        $wordPressPath
            ->method('path')
            ->willReturn($this->workspace . '/vendor/roots/wordpress');

        $sqliteIntegrationPluginPath = $this->createStub(SqliteDatabaseIntegrationPluginPath::class);
        $sqliteIntegrationPluginPath
            ->method('path')
            ->willReturn($this->workspace . '/wp-content/plugins/sqlite-database-integration');

        (new CreateSqliteDbDropIn(
            $wordPressPath,
            $sqliteIntegrationPluginPath,
        ))->execute();

        $this->assertFileExists($this->workspace . '/vendor/roots/wordpress/wp-content/db.php');
        $this->assertStringEqualsFile(
            $this->workspace . '/vendor/roots/wordpress/wp-content/db.php',
            $this->workspace . '/wp-content/plugins/sqlite-database-integration',
        );
    }
}
