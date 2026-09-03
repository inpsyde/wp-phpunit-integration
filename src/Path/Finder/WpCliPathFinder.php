<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path\Finder;

use Exception;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

readonly class WpCliPathFinder implements LocalDependencyPathFinder
{
    public function __construct(private LocalDependencyPath $packageRootPath)
    {
    }

    public function find(): string
    {
        $matches = (new Finder())
            ->ignoreDotFiles(false)
            ->ignoreVCS(false)
            ->in($this->packageRootPath->path())
            ->exclude('wp-content')
            ->files()
            ->name('wp');

        if (!$matches->hasResults()) {
            throw new Exception('Could not locate WP-CLI binary.');
        }

        $firstMatch = current(iterator_to_array($matches));

        if (!($firstMatch instanceof SplFileInfo)) {
            throw new Exception();
        }

        if (!$this->isWindows() && !$firstMatch->isExecutable()) {
            throw new Exception('The matched WP-CLI binary is not executable.');
        }

        return $firstMatch->getPathname();
    }

    private function isWindows(): bool
    {
        return \DIRECTORY_SEPARATOR === '\\';
    }
}
