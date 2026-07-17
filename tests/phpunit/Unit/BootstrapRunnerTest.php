<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit;

use Syde\WpPhpUnitIntegration\BootstrapLifecycle;
use Syde\WpPhpUnitIntegration\BootstrapRunner;
use Syde\WpPhpUnitIntegration\ShutdownFunctionRegisterer;
use Syde\WpPhpUnitIntegration\TestRunnerProcessTracker;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

final class BootstrapRunnerTest extends UnitTestCase
{
    public function testRunsPrepareAndLoadSequenceInChildProcess(): void
    {
        $bootstrapLifecycle = $this->createMock(BootstrapLifecycle::class);
        $bootstrapLifecycle->expects($this->never())->method('setup');
        $bootstrapLifecycle->expects($this->never())->method('cleanup');
        $bootstrapLifecycle->expects($this->once())->method('prepare');
        $bootstrapLifecycle->expects($this->once())->method('load');

        $testRunnerProcessTracker = $this->createMock(TestRunnerProcessTracker::class);
        $testRunnerProcessTracker
            ->expects($this->once())
            ->method('isCurrentProcessChildProcess')
            ->willReturn(true);

        $phpShutdownRegisterer = $this->createMock(ShutdownFunctionRegisterer::class);
        $phpShutdownRegisterer->expects($this->never())->method('register');

        (new BootstrapRunner(
            $bootstrapLifecycle,
            $testRunnerProcessTracker,
            $phpShutdownRegisterer,
        ))->execute();
    }

    public function testRunsFullSequenceInMainProcess(): void
    {
        $bootstrapLifecycle = $this->createMock(BootstrapLifecycle::class);
        $bootstrapLifecycle->expects($this->once())->method('setup');
        $bootstrapLifecycle->expects($this->exactly(2))->method('cleanup');
        $bootstrapLifecycle->expects($this->once())->method('prepare');
        $bootstrapLifecycle->expects($this->once())->method('load');

        $testRunnerProcessTracker = $this->createMock(TestRunnerProcessTracker::class);
        $testRunnerProcessTracker
            ->expects($this->once())
            ->method('isCurrentProcessChildProcess')
            ->willReturn(false);

        $shutdownRegisterer = $this->createMock(ShutdownFunctionRegisterer::class);
        $shutdownRegisterer->expects($this->once())->method('register')->willReturnCallback(
            static function (callable $cleanupCallback): void {
                $cleanupCallback();
            },
        );

        (new BootstrapRunner(
            $bootstrapLifecycle,
            $testRunnerProcessTracker,
            $shutdownRegisterer,
        ))->execute();
    }
}
