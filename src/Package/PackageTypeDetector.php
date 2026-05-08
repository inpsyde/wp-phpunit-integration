<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Package;

use Exception;

readonly class PackageTypeDetector
{
    public function __construct(private PackageComposerJsonReader $packageComposerJsonReader)
    {
    }

    /**
     * @throws Exception
     */
    public function determine(): PackageType
    {
        $composer = json_decode($this->packageComposerJsonReader->read(), true, 512, JSON_THROW_ON_ERROR);

        return PackageType::tryFrom($composer['type'] ?? '') ?? PackageType::Other;
    }
}
