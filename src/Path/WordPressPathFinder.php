<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

use Exception;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

readonly class WordPressPathFinder
{
    public function __construct(private PackageRootPath $packageRootPath)
    {
    }

    /**
     * @throws Exception
     */
    public function discoveredPath(): string
    {
        $finder = (new Finder())
            ->ignoreDotFiles(false)
            ->ignoreVCS(false)
            ->in($this->packageRootPath->path())
            ->files()
            ->name('wp-config-sample.php');

        if (!$finder->hasResults()) {
            throw new Exception('Could not locate WordPress installation.');
        }

        $firstMatch = current(iterator_to_array($finder));

        if (!($firstMatch instanceof SplFileInfo)) {
            throw new Exception();
        }

        return $firstMatch->getPath();
    }
}
