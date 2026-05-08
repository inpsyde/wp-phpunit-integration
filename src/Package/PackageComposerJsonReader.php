<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Package;

use Exception;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;

class PackageComposerJsonReader
{
    public function __construct(private readonly LocalDependencyPath $packageRootPath)
    {
    }

    /**
     * @throws Exception
     */
    public function read(): string
    {
        // TODO: Maybe use Filesystem to read it, as it's used in other places
        $content = @file_get_contents($this->packageRootPath->path() . '/composer.json');

        if ($content === false) {
            throw new Exception('Could not read package root composer.json.');
        }

        return $content;
    }
}
