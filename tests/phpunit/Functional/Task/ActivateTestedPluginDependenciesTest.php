<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use PHPUnit\Framework\Attributes\DataProvider;
use Syde\WpPhpUnitIntegration\Task\ActivateTestedPluginDependencies;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;
use Syde\WpPhpUnitIntegration\WpCli;

final class ActivateTestedPluginDependenciesTest extends FunctionalTestCase
{
    public function testPluginActivateNotCalledWhenNoRequiredPlugins(): void
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

    /**
     * @param list<array{0: list<string>, 1: string}> $wpCliArgsReturnMap
     */
    #[DataProvider('wpCliArgsReturnMapProvider')]
    public function testRequiredPluginsAreActivated(array $wpCliArgsReturnMap): void
    {
        $this->filesystem->mkdir($this->workspace . '/acme');

        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->exactly(count($wpCliArgsReturnMap)))
            ->method('run')
            ->willReturnMap($wpCliArgsReturnMap);

        (new ActivateTestedPluginDependencies(
            $this->localDependencyPath($this->workspace . '/acme'),
            $wpCli,
        ))->execute();
    }

    public static function wpCliArgsReturnMapProvider(): \Generator
    {
        yield [
            [
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
            ],
        ];

        yield [
            [
                [
                    [
                        'plugin',
                        'get',
                        'acme',
                        '--field=requires_plugins',
                        '--format=json',
                    ],
                    '"woocommerce,,  ,"',
                ],
                [
                    [
                        'plugin',
                        'activate',
                        'woocommerce',
                    ],
                    '',
                ],
            ],
        ];

        yield [
            [
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
            ],
        ];
    }
}
