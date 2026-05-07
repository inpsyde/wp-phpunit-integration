<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Unit\Package;

use Syde\PocWpLiteIntegrationTestHelper\Path\LocalDependencyPathNormalizer;
use Syde\PocWpLiteIntegrationTestHelper\Tests\UnitTestCase;

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
