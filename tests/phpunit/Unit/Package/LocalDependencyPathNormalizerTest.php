<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Package;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPathNormalizer;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

final class LocalDependencyPathNormalizerTest extends UnitTestCase
{
    public function testStripsTrailingSlashFromPath(): void
    {
        $this->assertSame(
            '/acme',
            (new LocalDependencyPathNormalizer())->normalize('/acme/'),
        );
    }
}
