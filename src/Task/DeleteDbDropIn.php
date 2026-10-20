<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Symfony\Component\Filesystem\Filesystem;

readonly class DeleteDbDropIn implements Task
{
    public function __construct(
        private WordPressPath $wordPressPath,
    ) {
    }

    public function execute(): void
    {
        (new Filesystem())->remove($this->wordPressPath->path() . '/wp-content/db.php');
    }
}
