<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\PHPUnit;

use PHPUnit\Event\Code\TestCollection;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Event\Telemetry\GarbageCollectorStatus;
use PHPUnit\Event\Telemetry\HRTime;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Event\Telemetry\MemoryUsage;
use PHPUnit\Event\Telemetry\Snapshot;
use PHPUnit\Event\TestSuite\Started;
use PHPUnit\Event\TestSuite\TestSuite;
use PHPUnit\Event\TestSuite\TestSuiteForTestClass;
use PHPUnit\Event\TestSuite\TestSuiteWithName;
use Syde\WpPhpUnitIntegration\DeferredWordPressLoad;
use Syde\WpPhpUnitIntegration\EnvVar;
use Syde\WpPhpUnitIntegration\PHPUnit\DeferredWordPressLoadSubscriber;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

final class DeferredWordPressLoadSubscriberTest extends UnitTestCase
{
    public function testEnablesDeferredLoadForAnnotatedTestClass(): void
    {
        $testClass = new #[DeferredWordPressLoad] class {
        };

        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->once())
            ->method('set')
            ->with(DeferredWordPressLoad::ENV_VAR, '1');

        (new DeferredWordPressLoadSubscriber($envVar))->notify(
            $this->testSuiteStartedForClass($testClass::class),
        );
    }

    public function testDisablesDeferredLoadForUnannotatedTestClass(): void
    {
        $testClass = new class {
        };

        $envVar = $this->createMock(EnvVar::class);
        $envVar
            ->expects($this->once())
            ->method('set')
            ->with(DeferredWordPressLoad::ENV_VAR, '0');

        (new DeferredWordPressLoadSubscriber($envVar))->notify(
            $this->testSuiteStartedForClass($testClass::class),
        );
    }

    public function testIgnoresSuiteThatIsNotForTestClass(): void
    {
        $envVar = $this->createMock(EnvVar::class);
        $envVar->expects($this->never())->method('set');

        (new DeferredWordPressLoadSubscriber($envVar))->notify(
            $this->testSuiteStarted(
                new TestSuiteWithName(
                    'test suite',
                    0,
                    TestCollection::fromArray([]),
                ),
            ),
        );
    }

    /**
     * @param class-string $className
     */
    private function testSuiteStartedForClass(string $className): Started
    {
        return $this->testSuiteStarted(
            new TestSuiteForTestClass(
                $className,
                0,
                TestCollection::fromArray([]),
                __FILE__,
                1,
            ),
        );
    }

    private function testSuiteStarted(TestSuite $testSuite): Started
    {
        return new Started($this->telemetryInfo(), $testSuite);
    }

    private function telemetryInfo(): Info
    {
        $duration = Duration::fromSecondsAndNanoseconds(0, 0);
        $memoryUsage = MemoryUsage::fromBytes(0);

        return new Info(
            new Snapshot(
                HRTime::fromSecondsAndNanoseconds(0, 0),
                $memoryUsage,
                $memoryUsage,
                new GarbageCollectorStatus(
                    0,
                    0,
                    0,
                    0,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                ),
            ),
            $duration,
            $memoryUsage,
            $duration,
            $memoryUsage,
        );
    }
}
