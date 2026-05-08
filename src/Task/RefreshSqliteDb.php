<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Syde\WpPhpUnitIntegration\PhpProcessIdProvider;
use Syde\WpPhpUnitIntegration\WpCli;
use Symfony\Component\Filesystem\Filesystem;

readonly class RefreshSqliteDb implements Task
{
    public function __construct(
        private LocalDependencyPath $wordPressPath,
        private PhpProcessIdProvider $phpProcessIdProvider,
        private WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        // WP-CLI's `wp db` commands currently do not work well with SQLite.
        // To work around this, each PHP process uses its own SQLite database file, created by copying the original database.
        // TODO: Revisit the approach taken once WP-CLI fully supports SQLite databases.
        $processDbFile = sprintf(".%s.sqlite", $this->phpProcessIdProvider->currentProcessId());
        $filesystem = new Filesystem();

        $this->wpCli->run([
            'config',
            'set',
            // https://github.com/WordPress/sqlite-database-integration/blob/4f3aab1a5b03b00f42d7b5141ac6a11f9c752256/packages/plugin-sqlite-database-integration/constants.php#L48
            'DB_FILE',
            $processDbFile,
        ]);

        $filesystem->copy(
            $this->wordPressPath->path() . '/wp-content/database/.ht.sqlite',
            $this->wordPressPath->path() . '/wp-content/database/' . $processDbFile,
        );
    }
}
