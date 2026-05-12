<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\WpCli;

readonly class EnableWpDebug implements Task
{
    public function __construct(
        private WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        $this->wpCli->run([
            'config',
            'set',
            'WP_DEBUG',
            'true',
        ]);

        $this->wpCli->run([
            'config',
            'set',
            'WP_DEBUG_LOG',
            'true',
        ]);

        $this->wpCli->run([
            'config',
            'set',
            'WP_DEBUG_DISPLAY',
            'false',
        ]);

        $this->wpCli->run([
            'config',
            'set',
            'SAVEQUERIES',
            'true',
        ]);
    }
}
