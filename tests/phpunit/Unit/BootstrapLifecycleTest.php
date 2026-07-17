<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit;

use Syde\WpPhpUnitIntegration\BootstrapLifecycle;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

final class BootstrapLifecycleTest extends UnitTestCase
{
    public function testRunsCustomizedLifecycleCallbacks(): void
    {
        $calls = [];
        $bootstrapLifecycle = new BootstrapLifecycle(
            static function () use (&$calls): void {
                $calls[] = 'setup';
            },
            static function () use (&$calls): void {
                $calls[] = 'load';
            },
            static function () use (&$calls): void {
                $calls[] = 'cleanup';
            },
            static function () use (&$calls): void {
                $calls[] = 'prepare';
            },
        );

        $bootstrapLifecycle->cleanup();
        $bootstrapLifecycle->setup();
        $bootstrapLifecycle->prepare();
        $bootstrapLifecycle->load();

        $this->assertSame(['cleanup', 'setup', 'prepare', 'load'], $calls);
    }
}
