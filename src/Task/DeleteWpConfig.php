<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Task;

use Syde\WpPhpunitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Filesystem\Filesystem;

class DeleteWpConfig implements Task
{
    public function __construct(
        private readonly LocalDependencyPath $wordPressPath,
    ) {
    }

    public function execute(): void
    {
        (new Filesystem())->remove($this->wordPressPath->path() . '/wp-config.php');
    }
}
