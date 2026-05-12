<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\WpCli;

readonly class DefineRequiredWpConstants implements Task
{
    public function __construct(
        private WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        // Some things might happen after WordPress was shut down. Better to avoid that.
        // https://github.com/WordPress/WordPress/blob/9d235f6c2d41e5d0f5a6b1a80d5ebd5673d09f0c/wp-includes/cron.php#L998-L1002
        $this->wpCli->run([
            'config',
            'set',
            'DISABLE_WP_CRON',
            'true',
            '--raw',
        ]);

        // The SQLite Database Integration plugin comes with multiple "drivers".
        // The AST "drivers" offers a better compatibility with MySQL than the default one.
        // https://make.wordpress.org/playground/2025/06/13/introducing-a-new-sqlite-driver-for-wordpress/
        // Until the AST is the default, we need to configure this constant.
        // https://github.com/WordPress/sqlite-database-integration/blob/535b42a935a778740387a8223c788f8d6155d5f8/wp-includes/sqlite/install-functions.php#L37-L44
        $this->wpCli->run([
            'config',
            'set',
            'WP_SQLITE_AST_DRIVER',
            'true',
            '--raw',
        ]);

        // To avoid Performance Lab-related plugin activation.
        // https://github.com/WordPress/sqlite-database-integration/blob/535b42a935a778740387a8223c788f8d6155d5f8/db.copy#L39-L58
        $this->wpCli->run([
            'config',
            'set',
            'SQLITE_MAIN_FILE',
            'true',
            '--raw',
        ]);
    }
}
