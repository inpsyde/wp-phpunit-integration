<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Task;

use Syde\PocWpLiteIntegrationTestHelper\Path\LocalDependencyPath;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class SymlinkTestedPlugin implements Task
{
    public function __construct(
        private readonly LocalDependencyPath $packageRootPath,
        private readonly LocalDependencyPath $wordPressPath,
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
