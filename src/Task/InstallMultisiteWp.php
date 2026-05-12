<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\WpCli;

readonly class InstallMultisiteWp implements Task
{
    public function __construct(
        private WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        // TODO: Maybe make these values configurable through environment variables or a configuration file.
        $this->wpCli->run([
            'core',
            'multisite-install',
            '--url=localhost:19254',
            '--title=WordPress Test Environment',
            '--admin_user=admin',
            '--admin_password=password',
            '--admin_email=admin@wordpress.localhost',
            '--skip-email',
        ]);
    }
}
