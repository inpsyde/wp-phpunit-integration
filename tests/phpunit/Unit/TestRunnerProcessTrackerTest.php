<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Unit;

use Syde\WpPhpunitIntegration\EnvVar;
use Syde\WpPhpunitIntegration\PhpProcessIdProvider;
use Syde\WpPhpunitIntegration\TestRunnerProcessTracker;
use Syde\WpPhpunitIntegration\Tests\UnitTestCase;

final class TestRunnerProcessTrackerTest extends UnitTestCase
{
    public function testDoesNotOverwriteMainProcessIdWhenAlreadyExists(): void
    {
        $phpProcessIdProvider = $this->createMock(PhpProcessIdProvider::class);
        $phpProcessIdProvider->expects($this->never())
            ->method('currentProcessId');

        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->once())
            ->method('get')
            ->willReturn('123456');

        (new TestRunnerProcessTracker(
            $phpProcessIdProvider,
            $envVar,
        ))->maybeStoreCurrentProcessAsMainProcess();
    }

    public function testStoresMainProcessIdWhenNoneExists(): void
    {
        $phpProcessIdProvider = $this->createMock(PhpProcessIdProvider::class);
        $phpProcessIdProvider
            ->expects($this->once())
            ->method('currentProcessId')
            ->willReturn(123456);

        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->once())
            ->method('get')
            ->willReturn(null);
        $envVar
            ->expects($this->once())
            ->method('set')->with(
                'WP_PHPUNIT_INTEGRATION_MAIN_PROCESS_ID',
                '123456',
            );

        (new TestRunnerProcessTracker(
            $phpProcessIdProvider,
            $envVar,
        ))->maybeStoreCurrentProcessAsMainProcess();
    }

    public function testThrowsExceptionBeforeIdIsStored(): void
    {
        $this->expectException(\Throwable::class);

        $phpProcessIdProvider = $this->createStub(PhpProcessIdProvider::class);

        $envVar = $this->createMock(EnvVar::class);
        $envVar->expects($this->once())
            ->method('get')
            ->willReturn(null);

        (new TestRunnerProcessTracker(
            $phpProcessIdProvider,
            $envVar,
        ))->isCurrentProcessMainProcess();
    }

    public function testReturnsTrueForMainProcess(): void
    {
        $phpProcessIdProvider = $this->createMock(PhpProcessIdProvider::class);
        $phpProcessIdProvider
            ->expects($this->once())
            ->method('currentProcessId')
            ->willReturn(123456);

        $envVar = $this->createMock(EnvVar::class);
        $envVar->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn('123456');

        $result = (new TestRunnerProcessTracker(
            $phpProcessIdProvider,
            $envVar,
        ))->isCurrentProcessMainProcess();

        $this->assertTrue($result);
    }

    public function testReturnsFalseForOtherProcess(): void
    {
        $phpProcessIdProvider = $this->createMock(PhpProcessIdProvider::class);
        $phpProcessIdProvider
            ->expects($this->once())
            ->method('currentProcessId')
            ->willReturn(456788);

        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->atLeastOnce())->method('get')
            ->willReturn('123456');

        $result = (new TestRunnerProcessTracker(
            $phpProcessIdProvider,
            $envVar,
        ))->isCurrentProcessMainProcess();

        $this->assertFalse($result);
    }

    public function testReturnsFalseWhenCurrentProcessIsMainProcess(): void
    {
        $testRunnerProcessTracker = $this
            ->getMockBuilder(TestRunnerProcessTracker::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isCurrentProcessMainProcess'])
            ->getMock();
        $testRunnerProcessTracker
            ->expects($this->once())
            ->method('isCurrentProcessMainProcess')
            ->willReturn(true);

        $this->assertFalse($testRunnerProcessTracker->isCurrentProcessChildProcess());
    }
}
