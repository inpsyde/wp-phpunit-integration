<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Path\Finder;

use Exception;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;

readonly class WpCliPathFinder implements LocalDependencyPathFinder
{
    public function __construct(private LocalDependencyPath $packageRootPath)
    {
    }

    public function find(): string
    {
        // Composer always exposes a required package's declared bin at this canonical
        // location, regardless of how deep in the dependency tree it's required from.
        // Searching the whole package tree for any file named "wp" (as this used to do) is
        // ambiguous: an installed WordPress plugin can ship its own same-named binary (e.g.
        // WooCommerce's release zip bundles a broken wp-cli/wp-cli composer bin-proxy at
        // wp-content/plugins/woocommerce/vendor/bin/wp), which can shadow the real one.
        $path = $this->packageRootPath->path() . '/vendor/bin/wp';

        if (!is_file($path)) {
            throw new Exception('Could not locate WP-CLI binary.');
        }

        if (!is_executable($path)) {
            throw new Exception('The matched WP-CLI binary is not executable.');
        }

        return $path;
    }
}
