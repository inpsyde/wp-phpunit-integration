<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

use Throwable;

readonly class WordPressPath implements LocalDependencyPath
{
    private const PACKAGE_NAME = 'roots/wordpress-no-content';

    public function __construct(
        private ComposerPackagePath $composerPackagePath,
        private WordPressPathFinder $wordPressPathFinder,
        private LocalDependencyPathNormalizer $localDependencyPathNormalizer,
    ) {
    }

    public function path(): string
    {
        try {
            $wordPressPath = $this->composerPackagePath->installPath(self::PACKAGE_NAME);
        } catch (Throwable) {
            $wordPressPath = $this->wordPressPathFinder->discoveredPath();
        }

        return $this->localDependencyPathNormalizer->normalize($wordPressPath);
    }
}
