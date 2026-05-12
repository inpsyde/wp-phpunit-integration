<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Task\ActivateTestedTheme;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;
use Syde\WpPhpUnitIntegration\WpCli;

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
