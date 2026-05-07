<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Task;

use Syde\WpPhpunitIntegration\WpCli;

class CreateWpConfig implements Task
{
    public function __construct(
        private readonly WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        // The --dbname and --dbuser are required by the WP-CLI, but since we are using the SQLite Database Integration, the value doesn't matter.
        // The --skip-check is needed to avoid WordPress checking the MySQL databases connection.
        $this->wpCli->run([
            'config',
            'create',
            '--dbname=',
            '--dbuser=',
            '--skip-check',
        ]);
    }
}
