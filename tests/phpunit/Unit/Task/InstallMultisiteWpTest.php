<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Unit\Task;

use Syde\PocWpLiteIntegrationTestHelper\Task\InstallMultisiteWp;
use Syde\PocWpLiteIntegrationTestHelper\Tests\UnitTestCase;
use Syde\PocWpLiteIntegrationTestHelper\WpCli;

final class InstallMultisiteWpTest extends UnitTestCase
{
    public function testRunsMultisiteInstallViaWpCli(): void
    {
        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->once())
            ->method('run')
            ->with(
                [
                    'core',
                    'multisite-install',
                    '--url=localhost:8889',
                    '--title=WordPress',
                    '--admin_user=admin',
                    '--admin_password=password',
                    '--admin_email=admin@wordpress.localhost',
                    '--skip-email',
                ],
            );

        (new InstallMultisiteWp($wpCli))->execute();
    }
}
