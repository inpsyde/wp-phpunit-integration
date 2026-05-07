<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Task;

use Syde\PocWpLiteIntegrationTestHelper\Path\LocalDependencyPath;
use Syde\PocWpLiteIntegrationTestHelper\PhpProcessIdProvider;
use Syde\PocWpLiteIntegrationTestHelper\WpCli;
use Symfony\Component\Filesystem\Filesystem;

class RefreshSqliteDb implements Task
{
    public function __construct(
        private readonly LocalDependencyPath $wordPressPath,
        private readonly PhpProcessIdProvider $phpProcessIdProvider,
        private readonly WpCli $wpCli,
    ) {
    }

    public function execute(): void
    {
        // Resetting the db with WP-CLI is not yet supported for SQLite.
        // https://github.com/wp-cli/db-command/pull/299
        // To overcome this, we use a separate database for each PHP process that is created by simply creating a copy of the database.
        $processDbFile = sprintf(".%s.sqlite", $this->phpProcessIdProvider->currentProcessId());
        $filesystem = new Filesystem();

        $this->wpCli->run([
            'config',
            'set',
            'DB_FILE',
            $processDbFile,
        ]);

        $filesystem->copy(
            $this->wordPressPath->path() . '/wp-content/database/.ht.sqlite',
            $this->wordPressPath->path() . '/wp-content/database/' . $processDbFile,
        );
    }
}
