<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

readonly class WordPressPath implements LocalDependencyPath
{
    private const PACKAGE_NAME = 'roots/wordpress';

    public function __construct(
        private ComposerPackagePath $composerPackagePath,
        private WordPressPathFinder $wordPressPathFinder,
        private LocalDependencyPathNormalizer $localDependencyPathNormalizer,
    ) {
    }

    public function path(): string
    {
        try {
            $wordPressPath = $this->composerPackagePath->installedPath(self::PACKAGE_NAME);
        } catch (\Throwable) {
            $wordPressPath = $this->wordPressPathFinder->discoveredPath();
        }

        return $this->localDependencyPathNormalizer->normalize($wordPressPath);
    }
}
