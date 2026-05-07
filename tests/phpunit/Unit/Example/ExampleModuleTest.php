<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Unit\Example;

use Syde\WpPhpunitIntegration\Tests\AbstractTestCase;

class ExampleModuleTest extends AbstractTestCase
{
    public function testExampleShouldTrueBeSameAsTrue(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertSame(true, true);
    }
}
