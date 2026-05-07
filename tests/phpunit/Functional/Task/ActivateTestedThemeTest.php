<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Task;

use Syde\WpPhpunitIntegration\Task\ActivateTestedTheme;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;
use Syde\WpPhpunitIntegration\WpCli;

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
