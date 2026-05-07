<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration;

use Inpsyde\Modularity\Package;
use Inpsyde\Modularity\Properties\LibraryProperties;

// phpcs:disable PSR1.Files.SideEffects
if (defined(__NAMESPACE__ . '\\APP_BOOTED')) {
    return;
}
const APP_BOOTED = 'wp-phpunit-integration.app-booted';

function package(): Package
{
    static $package;

    if ($package) {
        /** @var Package $package */
        return $package;
    }

    $basePath = wp_normalize_path(dirname(__DIR__));
    $properties = LibraryProperties::new("{$basePath}/composer.json");

    $package = Package::new($properties);
    $package->boot();

    return $package;
}
