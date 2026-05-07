<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Functional\Task;

use Syde\PocWpLiteIntegrationTestHelper\Task\ActivateTestedTheme;
use Syde\PocWpLiteIntegrationTestHelper\Tests\FunctionalTestCase;
use Syde\PocWpLiteIntegrationTestHelper\WpCli;

final class ActivateTestedThemeTest extends FunctionalTestCase
{
    public function testExecuteRunsThemeActivateViaWpCli(): void
    {
        // what happens when it's root?
        $this->filesystem->mkdir($this->workspace . '/acme');

        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->once())
            ->method('run')
            ->with(
                [
                    'theme',
                    'activate',
                    'acme',
                ],
            );

        (new ActivateTestedTheme(
            $this->localDependencyPath($this->workspace . '/acme'),
            $wpCli
        ))->execute();
    }
}
