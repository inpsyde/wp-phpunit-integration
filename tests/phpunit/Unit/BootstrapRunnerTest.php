<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit;

use Syde\WpPhpUnitIntegration\BootstrapLifecycle;
use Syde\WpPhpUnitIntegration\BootstrapRunner;
use Syde\WpPhpUnitIntegration\DeferredWordPressLoad;
use Syde\WpPhpUnitIntegration\EnvVar;
use Syde\WpPhpUnitIntegration\ShutdownFunctionRegisterer;
use Syde\WpPhpUnitIntegration\TestRunnerProcessTracker;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

final class BootstrapRunnerTest extends UnitTestCase
{
    public function testRunsPrepareAndLoadSequenceInEagerChildProcess(): void
    {
        $calls = [];
        $bootstrapLifecycle = $this->bootstrapLifecycle($calls);

        $testRunnerProcessTracker = $this->createMock(TestRunnerProcessTracker::class);
        $testRunnerProcessTracker
            ->expects($this->once())
            ->method('isCurrentProcessChildProcess')
            ->willReturn(true);

        $shutdownRegisterer = $this->createMock(ShutdownFunctionRegisterer::class);
        $shutdownRegisterer->expects($this->never())->method('register');

        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->once())
            ->method('get')
            ->with(DeferredWordPressLoad::ENV_VAR)
            ->willReturn(null);

        (new BootstrapRunner(
            $bootstrapLifecycle,
            $testRunnerProcessTracker,
            $shutdownRegisterer,
            $envVar,
        ))->execute();

        $this->assertSame(['prepare', 'load'], $calls);
    }

    public function testRunsOnlyPrepareSequenceInDeferredChildProcess(): void
    {
        $calls = [];
        $bootstrapLifecycle = $this->bootstrapLifecycle($calls);

        $testRunnerProcessTracker = $this->createMock(TestRunnerProcessTracker::class);
        $testRunnerProcessTracker
            ->expects($this->once())
            ->method('isCurrentProcessChildProcess')
            ->willReturn(true);

        $shutdownRegisterer = $this->createMock(ShutdownFunctionRegisterer::class);
        $shutdownRegisterer->expects($this->never())->method('register');

        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->once())
            ->method('get')
            ->with(DeferredWordPressLoad::ENV_VAR)
            ->willReturn('1');

        (new BootstrapRunner(
            $bootstrapLifecycle,
            $testRunnerProcessTracker,
            $shutdownRegisterer,
            $envVar,
        ))->execute();

        $this->assertSame(['prepare'], $calls);
    }

    public function testRunsFullSequenceInMainProcess(): void
    {
        $calls = [];
        $bootstrapLifecycle = $this->bootstrapLifecycle($calls);

        $testRunnerProcessTracker = $this->createMock(TestRunnerProcessTracker::class);
        $testRunnerProcessTracker
            ->expects($this->once())
            ->method('isCurrentProcessChildProcess')
            ->willReturn(false);

        $shutdownCallback = null;
        $shutdownRegisterer = $this->createMock(ShutdownFunctionRegisterer::class);
        $shutdownRegisterer
            ->expects($this->once())
            ->method('register')
            ->willReturnCallback(
                static function (callable $callback) use (&$shutdownCallback): void {
                    $shutdownCallback = $callback(...);
                },
            );

        $envVar = $this->createMock(EnvVar::class);
        $envVar->expects($this->never())->method('get');

        (new BootstrapRunner(
            $bootstrapLifecycle,
            $testRunnerProcessTracker,
            $shutdownRegisterer,
            $envVar,
        ))->execute();

        $this->assertSame(['cleanup', 'setup', 'prepare', 'load'], $calls);
        $this->assertInstanceOf(\Closure::class, $shutdownCallback);

        $shutdownCallback();

        $this->assertSame(['cleanup', 'setup', 'prepare', 'load', 'cleanup'], $calls);
    }

    /**
     * @param list<string> $calls
     */
    private function bootstrapLifecycle(array &$calls): BootstrapLifecycle
    {
        return new BootstrapLifecycle(
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
    }
}
