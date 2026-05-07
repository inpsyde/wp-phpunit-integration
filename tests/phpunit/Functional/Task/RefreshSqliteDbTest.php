<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Functional\Task;

use Syde\PocWpLiteIntegrationTestHelper\PhpProcessIdProvider;
use Syde\PocWpLiteIntegrationTestHelper\Task\RefreshSqliteDb;
use Syde\PocWpLiteIntegrationTestHelper\Tests\FunctionalTestCase;
use Syde\PocWpLiteIntegrationTestHelper\WpCli;

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
