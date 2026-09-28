<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

/**
 * phpcs:disable Syde.Files.LineLength.TooLong
 * @method void copy(string $originFile, string $targetFile, bool $overwriteNewerFiles = false)
 * @method void mkdir(string|iterable<string> $dirs, int $mode = 0o777)
 * @method bool exists(string|iterable<string> $files)
 * @method void touch(string|iterable<string> $files, ?int $time = null, ?int $atime = null)
 * @method void remove(string|iterable<string> $files)
 * @method void chmod(string|iterable<string> $files, int $mode, int $umask = 0o000, bool $recursive = false)
 * @method void chown(string|iterable<string> $files, string|int $user, bool $recursive = false)
 * @method void chgrp(string|iterable<string> $files, string|int $group, bool $recursive = false)
 * @method void rename(string $origin, string $target, bool $overwrite = false)
 * @method void symlink(string $originDir, string $targetDir, bool $copyOnWindows = false)
 * @method void hardlink(string $originFile, string|iterable<string> $targetFiles)
 * @method ?string readlink(string $path, bool $canonicalize = false)
 * @method string makePathRelative(string $endPath, string $startPath)
 * @method void mirror(string $originDir, string $targetDir, ?\Traversable<\SplFileInfo> $iterator = null, array<string, bool> $options = [])
 * @method bool isAbsolutePath(string $file)
 * @method string tempnam(string $dir, string $prefix, string $suffix = '')
 * @method void dumpFile(string $filename, $content)
 * @method void appendToFile(string $filename, $content, bool $lock = false)
 * @method string readFile(string $filename)
 * phpcs:enable
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
