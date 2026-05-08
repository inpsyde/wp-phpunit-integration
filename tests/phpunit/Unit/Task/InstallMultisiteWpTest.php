<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Task;

use Syde\WpPhpUnitIntegration\Task\InstallMultisiteWp;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;
use Syde\WpPhpUnitIntegration\WpCli;

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
                    '--title=WordPress Test Environment',
                    '--admin_user=admin',
                    '--admin_password=password',
                    '--admin_email=admin@wordpress.localhost',
                    '--skip-email',
                ],
            );

        (new InstallMultisiteWp($wpCli))->execute();
    }
}
