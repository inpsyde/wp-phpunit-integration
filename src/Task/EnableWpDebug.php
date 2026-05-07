<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Task;

use Syde\WpPhpunitIntegration\WpCli;

class EnableWpDebug implements Task
{
    public function __construct(
        private readonly WpCli $wpCli,
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
            'true',
        ]);

        $this->wpCli->run([
            'config',
            'set',
            'SAVEQUERIES',
            'true',
        ]);
    }
}
