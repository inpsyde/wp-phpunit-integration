<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class DeferredWordPressLoad
{
    public const ENV_VAR = 'WP_PHPUNIT_INTEGRATION_DEFERRED_WORDPRESS_LOAD';
}
