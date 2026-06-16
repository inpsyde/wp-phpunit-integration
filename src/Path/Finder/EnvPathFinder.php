<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path\Finder;

use Exception;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

readonly class EnvPathFinder implements LocalDependencyPathFinder
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
            ->files()
            ->name('.env');

        if (!$matches->hasResults()) {
            throw new Exception('Could not locate .env file.');
        }

        $firstMatch = current(iterator_to_array($matches));

        if (!($firstMatch instanceof SplFileInfo)) {
            throw new Exception();
        }

        return $firstMatch->getPathname();
    }
}
