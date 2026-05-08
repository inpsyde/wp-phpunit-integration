<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

use Exception;
use Syde\WpPhpUnitIntegration\Path\Finder\LocalDependencyPathFinder;

abstract readonly class DiscoveredPath implements LocalDependencyPath
{
    public function __construct(
        private LocalDependencyPathFinder $localDependencyPathFinder,
        private LocalDependencyPathNormalizer $localDependencyPathNormalizer,
    ) {
    }

    /**
     * @throws Exception
     */
    public function path(): string
    {
        return $this->localDependencyPathNormalizer->normalize(
            $this->localDependencyPathFinder->find(),
        );
    }
}
