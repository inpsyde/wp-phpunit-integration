<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Path\Finder;

use Exception;
use Syde\WpPhpunitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SqliteDatabaseIntegrationPluginFinder implements LocalDependencyPathFinder
{
    public function __construct(private readonly LocalDependencyPath $packageRootPath)
    {
    }

    public function find(): string
    {
        $matches = (new Finder())
            ->ignoreDotFiles(false)
            ->ignoreVCS(false)
            ->in($this->packageRootPath->path())
            ->files()
            ->name('db.copy');

        if (!$matches->hasResults()) {
            throw new Exception('Could not located SQLite Database Integration plugin.');
        }

        $firstMatch = current(iterator_to_array($matches));

        if (!($firstMatch instanceof SplFileInfo)) {
            throw new Exception();
        }

        return $firstMatch->getPath();
    }
}
