<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path;

readonly class SqliteDatabaseIntegrationPluginPath implements LocalDependencyPath
{
    private const PACKAGE_NAME = 'wpackagist-plugin/sqlite-database-integration';

    public function __construct(
        private ComposerPackagePath $composerPackagePath,
        private LocalDependencyPathNormalizer $localDependencyPathNormalizer,
    ) {
    }

    public function path(): string
    {
        return $this->localDependencyPathNormalizer->normalize(
            $this->composerPackagePath->installedPath(self::PACKAGE_NAME),
        );
    }
}
