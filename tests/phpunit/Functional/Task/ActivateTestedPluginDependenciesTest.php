<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Task\ActivateTestedPluginDependencies;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;
use Syde\WpPhpUnitIntegration\WpCli;

final class ActivateTestedPluginDependenciesTest extends FunctionalTestCase
{
    public function testNothingIsActivatedWhenThereAreNoRequiredPlugins(): void
    {
        $this->filesystem->mkdir($this->workspace . '/acme');

        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->once())
            ->method('run')
            ->with(
                [
                    'plugin',
                    'get',
                    'acme',
                    '--field=requires_plugins',
                    '--format=json',
                ],
            )
            ->willReturn('""');

        (new ActivateTestedPluginDependencies(
            $this->localDependencyPath($this->workspace . '/acme'),
            $wpCli,
        ))->execute();
    }

    public function testRequiredPluginIsActivated(): void
    {
        $this->filesystem->mkdir($this->workspace . '/acme');

        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->exactly(2))
            ->method('run')
            ->willReturnMap([
                [
                    [
                        'plugin',
                        'get',
                        'acme',
                        '--field=requires_plugins',
                        '--format=json',
                    ],
                    '"woocommerce"',
                ],
                [
                    [
                        'plugin',
                        'activate',
                        'woocommerce',
                    ],
                    '',
                ],
            ]);

        (new ActivateTestedPluginDependencies(
            $this->localDependencyPath($this->workspace . '/acme'),
            $wpCli,
        ))->execute();
    }

    public function testMultipleRequiredPluginsAreActivated(): void
    {
        $this->filesystem->mkdir($this->workspace . '/acme');

        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->exactly(3))
            ->method('run')
            ->willReturnMap([
                [
                    [
                        'plugin',
                        'get',
                        'acme',
                        '--field=requires_plugins',
                        '--format=json',
                    ],
                    '"woocommerce, woocommerce-subscriptions"',
                ],
                [
                    [
                        'plugin',
                        'activate',
                        'woocommerce',
                    ],
                    '',
                ],
                [
                    [
                        'plugin',
                        'activate',
                        'woocommerce-subscriptions',
                    ],
                    '',
                ],
            ]);

        (new ActivateTestedPluginDependencies(
            $this->localDependencyPath($this->workspace . '/acme'),
            $wpCli,
        ))->execute();
    }
}
