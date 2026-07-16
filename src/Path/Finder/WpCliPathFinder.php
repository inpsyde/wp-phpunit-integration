<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path\Finder;

use Exception;
use Syde\WpPhpUnitIntegration\EnvVar;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;

readonly class WpCliPathFinder implements LocalDependencyPathFinder
{
    public function __construct(
        private LocalDependencyPath $packageRootPath,
        private EnvVar $envVar,
    ) {
    }

    public function find(): string
    {
        $path = $this->resolveBinDir() . '/wp';

        if (!is_file($path)) {
            throw new Exception('Could not locate WP-CLI binary.');
        }

        if (!is_executable($path)) {
            throw new Exception('The matched WP-CLI binary is not executable.');
        }

        return $path;
    }

    /**
     * Composer always exposes a required package's declared bin at this location, regardless
     * of how deep in the dependency tree it's required from - but the location itself is
     * configurable. This replicates Composer's own resolution (see Composer\Config::get()):
     * the COMPOSER_BIN_DIR environment variable wins, then the "bin-dir" config key, then it
     * falls back to "{vendor-dir}/bin", where vendor-dir follows the same precedence.
     */
    private function resolveBinDir(): string
    {
        $root = $this->packageRootPath->path();
        $config = $this->readComposerConfig($root);

        $binDir = $this->envVar->get('COMPOSER_BIN_DIR') ?? $config['bin-dir'] ?? null;

        return $binDir !== null
            ? $this->toAbsolutePath($root, $binDir)
            : $this->resolveVendorDir($root, $config) . '/bin';
    }

    /**
     * @param array<string, mixed> $config
     */
    private function resolveVendorDir(string $root, array $config): string
    {
        $vendorDir = $this->envVar->get('COMPOSER_VENDOR_DIR') ?? $config['vendor-dir'] ?? null;

        return $this->toAbsolutePath($root, $vendorDir ?? 'vendor');
    }

    private function toAbsolutePath(string $root, string $path): string
    {
        return preg_match('#^(/|[a-zA-Z]:[\\\\/])#', $path) === 1 ? $path : "{$root}/{$path}";
    }

    /**
     * @return array<string, mixed>
     */
    private function readComposerConfig(string $root): array
    {
        $contents = file_get_contents($root . '/composer.json');
        if ($contents === false) {
            throw new Exception('Could not read composer.json.');
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded['config'] ?? null) ? $decoded['config'] : [];
    }
}
