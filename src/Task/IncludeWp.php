<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;

readonly class IncludeWp implements Task
{
    public function __construct(
        private LocalDependencyPath $wordPressPath,
    ) {
    }

    public function execute(): void
    {
        include $this->wordPressPath->path() . '/wp-load.php';
    }
}
