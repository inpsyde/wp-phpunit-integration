<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path\Finder;

use Exception;

interface LocalDependencyPathFinder
{
    /**
     * @throws Exception
     */
    public function find(): string;
}
