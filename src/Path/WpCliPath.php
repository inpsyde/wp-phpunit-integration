<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

use Exception;

readonly class WpCliPath implements LocalDependencyPath
{
    private const PACKAGE_NAME = 'johnpbloch/wp-cli-phar';

    public function __construct(
        private ComposerPackagePath $composerPackagePath,
        private LocalDependencyPathNormalizer $localDependencyPathNormalizer,
    ) {
    }

    /**
     * @throws Exception
     */
    public function path(): string
    {
        return $this->localDependencyPathNormalizer->normalize(
            $this->composerPackagePath->installedPath(self::PACKAGE_NAME),
        ) . '/wp';
    }
}
