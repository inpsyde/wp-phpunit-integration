<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Task\ActivateTestedPlugin;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;
use Syde\WpPhpUnitIntegration\WpCli;

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
