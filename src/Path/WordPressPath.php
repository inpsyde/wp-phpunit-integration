<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

use Throwable;

readonly class WordPressPath implements LocalDependencyPath
{
    // The composer.json file requires the roots/wordpress meta-package, which in turn requires the no-content package that contains the WordPress files.
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
