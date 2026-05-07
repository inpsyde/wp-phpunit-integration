<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Task;

use Syde\PocWpLiteIntegrationTestHelper\Path\LocalDependencyPath;
use Symfony\Component\Filesystem\Filesystem;

class CreateEmptyWpThemesDir implements Task
{
    public function __construct(
        private readonly LocalDependencyPath $wordPressPath,
    ) {
    }

    public function execute(): void
    {
        // When somebody requires WordPress without the "wp-content" using, for example, "roots/wordpress-no-content", then the themes directory will not exist.
        // WordPress complains if the directory does not exist.
        // https://github.com/WordPress/WordPress/blob/9d235f6c2d41e5d0f5a6b1a80d5ebd5673d09f0c/wp-includes/theme.php#L4355
        $filesystem = new Filesystem();
        $filesystem->mkdir($this->wordPressPath->path() . '/wp-content/themes/');
    }
}
