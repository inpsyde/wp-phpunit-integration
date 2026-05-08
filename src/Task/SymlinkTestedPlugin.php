<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class SymlinkTestedPlugin implements Task
{
    public function __construct(
        private LocalDependencyPath $packageRootPath,
        private LocalDependencyPath $wordPressPath,
    ) {
    }

    public function execute(): void
    {
        $name = Path::getFilenameWithoutExtension($this->packageRootPath->path());

        (new Filesystem())->symlink(
            $this->packageRootPath->path(),
            $this->wordPressPath->path() . '/wp-content/plugins/' . $name,
        );
    }
}
