<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Path\PackageRootPath;
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

        $packageRootPath = $this->createStub(PackageRootPath::class);
        $packageRootPath
            ->method('path')
            ->willReturn($this->workspace . '/acme');

        (new ActivateTestedPlugin(
            $packageRootPath,
            $wpCli,
        ))->execute();
    }
}
