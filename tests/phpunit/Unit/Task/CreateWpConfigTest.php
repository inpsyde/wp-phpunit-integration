<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Unit\Task;

use Syde\PocWpLiteIntegrationTestHelper\Task\CreateWpConfig;
use Syde\PocWpLiteIntegrationTestHelper\Tests\UnitTestCase;
use Syde\PocWpLiteIntegrationTestHelper\WpCli;

final class CreateWpConfigTest extends UnitTestCase
{
    public function testRunsConfigCreateViaWpCli(): void
    {
        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->once())
            ->method('run')
            ->with(
                [
                    'config',
                    'create',
                    '--dbname=',
                    '--dbuser=',
                    '--skip-check',
                ],
            );

        (new CreateWpConfig($wpCli))->execute();
    }
}
