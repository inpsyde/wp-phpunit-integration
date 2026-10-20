<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

use Exception;

interface LocalDependencyPath
{
    /**
     * @throws Exception
     */
    public function path(): string;
}
