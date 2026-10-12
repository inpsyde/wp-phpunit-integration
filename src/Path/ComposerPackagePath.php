<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

use Composer\InstalledVersions;
use Exception;

class ComposerPackagePath
{
    /**
     * @throws Exception
     */
    public function installedPath(string $package): string
    {
        $installPath = InstalledVersions::getInstallPath($package);

        if ($installPath === null) {
            throw new Exception("Package $package is not installed.");
        }

        $installRealPath = realpath($installPath);

        if ($installRealPath === false) {
            throw new Exception("Can't determine real path of $package.");
        }

        return $installRealPath;
    }
}
