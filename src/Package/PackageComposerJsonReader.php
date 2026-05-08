<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Package;

use Exception;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Filesystem\Filesystem;

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
        return (new Filesystem())->readFile($this->packageRootPath->path() . '/composer.json');
    }
}
