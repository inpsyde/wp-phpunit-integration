<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

use Exception;
use Symfony\Component\Filesystem\Filesystem;

class PackageRootPath implements LocalDependencyPath
{
    /**
     * @throws Exception
     */
    public function __construct(
        private readonly string $packageRootPath,
        private readonly LocalDependencyPathNormalizer $localDependencyPathNormalizer,
    ) {
        if (!(new Filesystem())->exists($this->packageRootPath)) {
            throw new Exception('Package root path does not exist.');
        }
    }

    public function path(): string
    {
        return $this->localDependencyPathNormalizer->normalize($this->packageRootPath);
    }
}
