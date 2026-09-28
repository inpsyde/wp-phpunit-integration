<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

/**
 * @method void copy(string $originFile, string $targetFile, bool $overwriteNewerFiles = false)
 * @method void mkdir(string|iterable<string> $dirs, int $mode = 0o777)
 * @method bool exists(string|iterable<string> $files)
 * @method void remove(string|iterable<string> $files)
 * @method void symlink(string $originDir, string $targetDir, bool $copyOnWindows = false)
 * @method void dumpFile(string $filename, $content)
 */
readonly class Filesystem
{
    public function __construct(
        private SymfonyFilesystem $symfonyFilesystem,
        private LocalDependencyPath $packageRootPath,
        private LocalDependencyPath $wordPressPath,
    ) {
    }

    public function packageRootPath(): string
    {
        return $this->packageRootPath->path();
    }

    public function wordPressPath(): string
    {
        return $this->wordPressPath->path();
    }

    /**
     * @param list<mixed> $arguments
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (!method_exists($this->symfonyFilesystem, $method)) {
            throw new \BadMethodCallException();
        }

        return $this->symfonyFilesystem->{$method}(...$arguments);
    }
}
