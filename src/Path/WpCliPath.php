<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

readonly class WpCliPath implements LocalDependencyPath
{
    private const PACKAGE_NAME = 'johnpbloch/wp-cli-phar';

    public function __construct(
        private ComposerPackagePath $composerPackagePath,
        private LocalDependencyPathNormalizer $localDependencyPathNormalizer,
    ) {
    }

    public function path(): string
    {
        return $this->localDependencyPathNormalizer->normalize(
            $this->composerPackagePath->installPath(self::PACKAGE_NAME),
        ) . '/wp';
    }
}
