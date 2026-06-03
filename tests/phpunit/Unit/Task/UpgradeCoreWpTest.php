<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Task;

use Syde\WpPhpUnitIntegration\EnvVar;
use Syde\WpPhpUnitIntegration\Task\UpgradeCoreWp;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;
use Syde\WpPhpUnitIntegration\WpCli;

final class UpgradeCoreWpTest extends UnitTestCase
{
    public function testRunsCoreUpgradeWhenEnvVarDefined(): void
    {
        $envVar = $this->createMock(EnvVar::class);
        $envVar->method('get')->with('WP_PHPUNIT_INTEGRATION_WP_CORE_VERSION')->willReturn('3.1');

        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->once())
            ->method('run')
            ->with(
                [
                    'core',
                    'upgrade',
                    '--version=3.1',
                    '--force',
                ],
            );

        (new UpgradeCoreWp($envVar, $wpCli))->execute();
    }

    public function testSkipsCoreUpgradeWhenEnvVarNotDefined(): void
    {
        $envVar = $this->createMock(EnvVar::class);
        $envVar->method('get')->with('WP_PHPUNIT_INTEGRATION_WP_CORE_VERSION')->willReturn(null);

        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->never())
            ->method('run');

        (new UpgradeCoreWp($envVar, $wpCli))->execute();
    }
}
