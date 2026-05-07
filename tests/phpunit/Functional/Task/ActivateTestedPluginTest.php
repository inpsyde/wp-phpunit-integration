<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Functional\Task;

use Syde\PocWpLiteIntegrationTestHelper\Task\ActivateTestedPlugin;
use Syde\PocWpLiteIntegrationTestHelper\Tests\FunctionalTestCase;
use Syde\PocWpLiteIntegrationTestHelper\WpCli;

final class ActivateTestedPluginTest extends FunctionalTestCase
{
    public function testActivatesPluginViaWpCli(): void
    {
        // what happens when it's root?
        $this->filesystem->mkdir($this->workspace . '/acme');

        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->once())
            ->method('run')
            ->with(
                [
                    'plugin',
                    'activate',
                    'acme',
                ],
            );

        (new ActivateTestedPlugin(
            $this->localDependencyPath($this->workspace . '/acme'),
            $wpCli
        ))->execute();
    }
}
