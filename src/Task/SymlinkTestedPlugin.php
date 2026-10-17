<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

use Syde\WpPhpUnitIntegration\Path\PackageRootPath;
use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class SymlinkTestedPlugin implements Task
{
    public function __construct(
        private PackageRootPath $packageRootPath,
        private WordPressPath $wordPressPath,
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
