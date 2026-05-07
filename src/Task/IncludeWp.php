<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Task;

use Syde\PocWpLiteIntegrationTestHelper\Path\LocalDependencyPath;

class IncludeWp implements Task
{
    public function __construct(
        private readonly LocalDependencyPath $wordPressPath,
    ) {
    }

    public function execute(): void
    {
        include $this->wordPressPath->path() . '/wp-load.php';
    }
}
