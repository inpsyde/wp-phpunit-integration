<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Path;

use Exception;
use Syde\WpPhpunitIntegration\Path\Finder\LocalDependencyPathFinder;

abstract class DiscoveredPath implements LocalDependencyPath
{
    public function __construct(
        private readonly LocalDependencyPathFinder $localDependencyPathFinder,
        private readonly LocalDependencyPathNormalizer $localDependencyPathNormalizer,
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
