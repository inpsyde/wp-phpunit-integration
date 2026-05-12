<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\PhpProcessIdProvider;
use Syde\WpPhpUnitIntegration\Task\RefreshSqliteDb;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;
use Syde\WpPhpUnitIntegration\WpCli;

final class RefreshSqliteDbTest extends FunctionalTestCase
{
    public function testCopiesMainDatabaseToProcessSpecificFileAndSetsConstantViaWpCli(): void
    {
        $this->filesystem->appendToFile(
            $this->workspace . '/wordpress/wp-content/database/.ht.sqlite',
            'Initial db state',
        );

        $phpProcessIdProvider = $this->createMock(PhpProcessIdProvider::class);
        $phpProcessIdProvider
            ->expects($this->once())
            ->method('currentProcessId')->willReturn(123456);

        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->once())
            ->method('run')
            ->with(
                [
                    'config',
                    'set',
                    'DB_FILE',
                    '.123456.sqlite',
                ],
            );

        (new RefreshSqliteDb(
            $this->localDependencyPath($this->workspace . '/wordpress'),
            $phpProcessIdProvider,
            $wpCli,
        ))->execute();

        $this->assertFileExists($this->workspace . '/wordpress/wp-content/database/.123456.sqlite');
        $this->assertStringEqualsFile(
            $this->workspace . '/wordpress/wp-content/database/.123456.sqlite',
            'Initial db state',
        );
    }
}
