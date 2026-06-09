<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Task;

use Syde\WpPhpUnitIntegration\EnvVar;
use Syde\WpPhpUnitIntegration\Task\MaybeUpgradeCoreWp;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;
use Syde\WpPhpUnitIntegration\WpCli;

final class MaybeUpgradeCoreWpTest extends UnitTestCase
{
    public function testRunsCoreUpgradeWhenPrefixedEnvVarDefined(): void
    {
        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->once())
            ->method('get')
            ->with('WP_PHPUNIT_INTEGRATION_WP_CORE_VERSION')
            ->willReturn('3.1');

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

        (new MaybeUpgradeCoreWp($envVar, $wpCli))->execute();
    }

    public function testRunsCoreUpgradeWhenShortEnvVarDefined(): void
    {
        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(
                static function (string $key): ?string {
                    return match ($key) {
                        'WP_PHPUNIT_INTEGRATION_WP_CORE_VERSION' => null,
                        'WP_CORE_VERSION' => '3.1',
                        default => throw new \Exception(
                            'Unexpected WordPress core version env retrieved.'
                        ),
                    };
                },
            );

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

        (new MaybeUpgradeCoreWp($envVar, $wpCli))->execute();
    }

    public function testSkipsCoreUpgradeWhenEnvVarsNotDefined(): void
    {
        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturn(null);

        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->never())
            ->method('run');

        (new MaybeUpgradeCoreWp($envVar, $wpCli))->execute();
    }
}
