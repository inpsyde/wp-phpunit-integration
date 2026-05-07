<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Unit\Task;

use Syde\WpPhpunitIntegration\Task\CreateWpConfig;
use Syde\WpPhpunitIntegration\Tests\UnitTestCase;
use Syde\WpPhpunitIntegration\WpCli;

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
