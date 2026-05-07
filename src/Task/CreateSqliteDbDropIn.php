<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Task;

use Syde\PocWpLiteIntegrationTestHelper\Path\LocalDependencyPath;
use Symfony\Component\Filesystem\Filesystem;

class CreateSqliteDbDropIn implements Task
{
    public function __construct(
        private readonly LocalDependencyPath $wordPressPath,
        private readonly LocalDependencyPath $sqliteIntegrationPluginPath,
    ) {
    }

    public function execute(): void
    {
        $filesystem = new Filesystem();
        // The database drop-in file has to be created before WordPress is installed, otherwise it will try the "regular way" using a MySQL database.
        // The SQLite Database Integration comes with a template for the drop-in that's also used "internally" by them.
        // https://github.com/WordPress/sqlite-database-integration/blob/535b42a935a778740387a8223c788f8d6155d5f8/activate.php#L72-L134
        $templateFile = $this->sqliteIntegrationPluginPath->path() . '/db.copy';

        // The Filesystem 6.4 LTS (PHP 8.1) doesn't have the "readFile" method. However, newer versions do.
        // We should circle back to this and decide on the exact supported versions and improve this.
        $dbDropInTemplate = method_exists($filesystem, 'readFile')
            ? $filesystem->readFile($templateFile)
            : file_get_contents($templateFile);

        // The SQLite Database Integration assumes a certain location by default, however, there's a chance that the plugin is installed somewhere else.
        // https://github.com/WordPress/sqlite-database-integration/blob/535b42a935a778740387a8223c788f8d6155d5f8/db.copy#L16-L20
        // It's the safest if we set the location explicitly.
        // When installed with Composer, the location is controlled using the "extra.installer-paths".
        $dbDropIn = str_replace(
            '{SQLITE_IMPLEMENTATION_FOLDER_PATH}',
            $this->sqliteIntegrationPluginPath->path(),
            $dbDropInTemplate,
        );

        $filesystem->dumpFile(
            $this->wordPressPath->path() . '/wp-content/db.php',
            $dbDropIn,
        );
    }
}
