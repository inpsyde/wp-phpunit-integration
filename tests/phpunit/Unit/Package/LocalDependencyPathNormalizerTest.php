<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Unit\Package;

use Syde\WpPhpunitIntegration\Path\LocalDependencyPathNormalizer;
use Syde\WpPhpunitIntegration\Tests\UnitTestCase;

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
