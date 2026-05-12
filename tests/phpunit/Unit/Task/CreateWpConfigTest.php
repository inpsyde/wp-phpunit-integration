<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Task;

use Syde\WpPhpUnitIntegration\Task\CreateWpConfig;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;
use Syde\WpPhpUnitIntegration\WpCli;

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
