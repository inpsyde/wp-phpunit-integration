<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Filesystem\Filesystem;

// phpcs:ignore Syde.NamingConventions.ElementNameMinimalLength.TooShort
readonly class Fs
{
    private Filesystem $filesystem;

    public function __construct(
        public LocalDependencyPath $packageRootPath,
        public LocalDependencyPath $wordPressPath,
    ) {
        $this->filesystem = new Filesystem();
    }

    public function copy(string $fromPath, string $toPath): void
    {
        $this->filesystem->copy($fromPath, $toPath);
    }

    public function remove(string $path): void
    {
        $this->filesystem->remove($path);
    }

    public function createDir(string $path): void
    {
        $this->filesystem->mkdir($path);
    }

    public function writeFile(string $path, string $content): void
    {

        $this->filesystem->dumpFile($path, $content);
    }
}
