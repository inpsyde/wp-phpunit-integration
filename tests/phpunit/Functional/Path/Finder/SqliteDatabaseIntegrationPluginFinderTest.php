<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Path\Finder;

use Syde\WpPhpUnitIntegration\Path\Finder\SqliteDatabaseIntegrationPluginFinder;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

final class SqliteDatabaseIntegrationPluginFinderTest extends FunctionalTestCase
{
    public function testLocatesSqlitePluginWhenDbCopyFileExists(): void
    {
        $this->filesystem->appendToFile(
            $this->workspace . '/acme/wp-content/plugins/sqlite-database-integration/db.copy',
            '',
        );

        $sqliteDatabaseIntegrationPluginFinder = new SqliteDatabaseIntegrationPluginFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        );

        $this->assertSame(
            $this->workspace . '/acme/wp-content/plugins/sqlite-database-integration',
            $sqliteDatabaseIntegrationPluginFinder->find(),
        );
    }

    public function testThrowsExceptionWhenSqlitePluginNotFound(): void
    {
        $this->expectExceptionMessageMatches(
            '/Could not located SQLite Database Integration plugin/',
        );

        $this->filesystem->mkdir($this->workspace . '/acme');

        (new SqliteDatabaseIntegrationPluginFinder(
            $this->localDependencyPath($this->workspace . '/acme'),
        ))->find();
    }
}
