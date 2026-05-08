<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Filesystem\Filesystem;

class DeleteSqliteDatabaseDir implements Task
{
    public function __construct(
        private readonly LocalDependencyPath $wordPressPath,
    ) {
    }

    public function execute(): void
    {
        (new Filesystem())->remove($this->wordPressPath->path() . '/wp-content/database/');
    }
}
